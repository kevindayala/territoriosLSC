<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Models\City;
use App\Models\Territory;

class TerritoryController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $isAdminOrCapitan = $user && $user->hasAnyRole(['admin', 'capitan']);

        $excludePersonalAssignments = function ($q) use ($user) {
            $q->whereNull('completed_at')
                ->where('type', 'personal')
                ->where('assigned_to_user_id', '!=', optional($user)->id);
        };

        $topAssignedTerritories = collect();
        if (!$request->has('form_submitted') || (!$request->filled('search') && empty($request->filter))) {
            $topAssignedTerritoriesQuery = Territory::withCount([
                'persons',
                'assignments as is_assigned' => function ($q) {
                    $q->whereNull('completed_at')->whereDate('assigned_at', '<=', now()->toDateString());
                },
                'assignments as annual_completions_count' => function ($query) {
                    // Service Year starts on September 1st
                    $startYear = now()->month >= 9 ? now()->year : now()->year - 1;
                    $startDate = \Carbon\Carbon::create($startYear, 9, 1)->startOfDay();

                    $query->whereNotNull('completed_at')->where('completed_at', '>=', $startDate);
                }
            ])->with([
                        'city',
                        'assignments' => function ($q) {
                            $q->whereNull('completed_at')->with(['assignedBy', 'assignedTo'])->latest('id');
                        }
                    ])->whereHas('assignments', function ($q) {
                        $q->whereNull('completed_at')->whereDate('assigned_at', '<=', now()->toDateString());
                    });

            if (!$isAdminOrCapitan) {
                $topAssignedTerritoriesQuery->whereDoesntHave('assignments', $excludePersonalAssignments);
            }

            if ($request->filled('city_id') && $request->city_id !== 'todas') {
                $cityIds = \App\Models\City::where('id', $request->city_id)->orWhere('parent_id', $request->city_id)->pluck('id');
                $topAssignedTerritoriesQuery->whereIn('territories.city_id', $cityIds);
            }

            $topAssignedTerritories = $topAssignedTerritoriesQuery
                ->orderBy(\App\Models\City::select('name')->whereColumn('cities.id', 'territories.city_id'))
                ->orderByRaw('LENGTH(territories.code) ASC')
                ->orderBy('territories.code', 'asc')
                ->get();
        }



        $query = Territory::withCount([
            'persons',
            'assignments as is_assigned' => function ($q) {
                $q->whereNull('completed_at')
                    ->whereDate('assigned_at', '<=', now()->toDateString());
            },
            'assignments as is_my_assignment' => function ($q) use ($user) {
                if ($user) {
                    $q->whereNull('completed_at')
                        ->where('assigned_to_user_id', $user->id);
                } else {
                    $q->whereRaw('0 = 1');
                }
            },
            'assignments as annual_completions_count' => function ($query) {
                // Service Year starts on September 1st
                $startYear = now()->month >= 9 ? now()->year : now()->year - 1;
                $startDate = \Carbon\Carbon::create($startYear, 9, 1)->startOfDay();

                $query->whereNotNull('completed_at')->where('completed_at', '>=', $startDate);
            }
        ])->with([
                    'city',
                    'assignments' => function ($q) {
                        $q->whereNull('completed_at')
                            ->whereDate('assigned_at', now()->toDateString())
                            ->with('assignedBy')
                            ->latest('id')
                            ->limit(1);
                    }
                ]);

        if (!$isAdminOrCapitan) {
            $query->whereDoesntHave('assignments', $excludePersonalAssignments);
        }

        if ($request->filled('city_id') && $request->city_id !== 'todas') {
            $cityIds = \App\Models\City::where('id', $request->city_id)->orWhere('parent_id', $request->city_id)->pluck('id');
            $query->whereIn('territories.city_id', $cityIds);
        }

        // View Mode Filter
        if ($request->filled('filter')) {
            $filters = (array) $request->filter;

            $query->where(function ($q) use ($user, $filters) {
                if (in_array('assigned_today', $filters)) {
                    $q->orWhereHas('assignments', function ($subq) {
                        $subq->whereNull('completed_at')
                            ->whereDate('assigned_at', now()->toDateString());
                    });
                }
                if (in_array('my_assignments', $filters)) {
                    $q->orWhereHas('assignments', function ($subq) use ($user) {
                        $subq->whereNull('completed_at');
                        if ($user) {
                            $subq->where('assigned_to_user_id', $user->id);
                        }
                    });
                }
                if (in_array('available', $filters)) {
                    $q->orWhereDoesntHave('assignments', function ($subq) {
                        $subq->whereNull('completed_at');
                    });
                }
                if (in_array('recommended', $filters)) {
                    $q->orWhere(function ($subq) {
                        $subq->whereDoesntHave('assignments', function ($subq2) {
                            $subq2->whereNull('completed_at');
                        })->where(function ($subq3) {
                            $subq3->whereNull('last_completed_at')
                                ->orWhere('last_completed_at', '<=', now()->subMonths(6));
                        });
                    });
                }
            });
        }

        // Search logic
        if ($request->filled('search')) {
            $search = $request->search;
            $searchWithoutSpaces = str_replace(' ', '', $search);

            $query->where(function ($q) use ($search, $searchWithoutSpaces) {
                $q->whereRaw("REPLACE(territories.code, ' ', '') LIKE ?", ["%{$searchWithoutSpaces}%"])
                    ->orWhere('territories.neighborhood_name', 'like', "%{$search}%");
            });
        }

        // 1. Group by city if "Todas" is active or none is explicitly chosen
        if (!$request->filled('city_id') || $request->city_id === 'todas') {
            $query->orderBy(\App\Models\City::select('name')->whereColumn('cities.id', 'territories.city_id'));
        }

        // 2. Exact Sort Order
        if ($request->filled('filter')) {
            $date6Months = now()->subMonths(6)->toDateTimeString();
            $date2Months = now()->subMonths(2)->toDateTimeString();

            $query->orderByRaw("
                CASE 
                    WHEN is_my_assignment > 0 THEN 0
                    WHEN is_assigned > 0 THEN 5
                    WHEN territories.last_completed_at IS NULL THEN 1
                    WHEN territories.last_completed_at <= '{$date6Months}' THEN 2
                    WHEN territories.last_completed_at <= '{$date2Months}' THEN 3
                    ELSE 4
                END ASC
            ");
        }

        $query->orderByRaw('LENGTH(territories.code) ASC')
            ->orderBy('territories.code', 'asc');

        $hasGlobalFilters = $request->filled('search') || !empty($request->filter);
        $hasCitySelected = $request->filled('city_id') && $request->city_id !== 'todas';

        if (!$hasGlobalFilters && !$hasCitySelected && !$request->has('form_submitted')) {
            $query->whereRaw('0 = 1');
        }

        $territories = $query->paginate(50)->withQueryString();

        $cities = City::hierarchical()->where('cities.is_active', true)->withCount('territories')->get();

        return view('territories.index', compact('territories', 'cities', 'topAssignedTerritories'));
    }

    public function create()
    {
        $cities = City::hierarchical()->where('cities.is_active', true)->get();
        return view('territories.create', compact('cities'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => [
                'required',
                'string',
                Rule::unique('territories', 'code')->whereNull('deleted_at')
            ],
            'city_id' => 'required|exists:cities,id',
            'neighborhood_name' => 'required|string|max:255',
            'status' => 'required|in:active,inactive',
            'notes' => 'nullable|string',
        ]);

        // Cleanup any soft-deleted territory with the same code to avoid DB unique constraint issues
        Territory::onlyTrashed()->where('code', $request->code)->forceDelete();

        Territory::create($validated);

        $redirectTo = $request->input('redirect_to');
        if ($redirectTo === 'admin') {
            return redirect()->route('admin.territories.index')->with('success', 'Territorio creado exitosamente.');
        }

        return redirect()->route('territories.index')->with('success', 'Territorio creado exitosamente.');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function show(Territory $territory)
    {
        $territory->load(['city', 'assignments.assignedTo', 'assignments.assignedBy', 'persons']);

        $recentCompletionWarning = false;
        if ($territory->last_completed_at && \Carbon\Carbon::parse($territory->last_completed_at)->gt(now()->subMonths(2))) {
            $recentCompletionWarning = true;
        }

        // Check if currently assigned
        $currentAssignment = $territory->assignments->whereNull('completed_at')->sortByDesc('assigned_at')->first();

        return view('territories.show', compact('territory', 'recentCompletionWarning', 'currentAssignment'));
    }

    public function reorderPersons(Request $request, Territory $territory)
    {
        abort_unless(auth()->user()->hasAnyRole(['admin', 'capitan']), 403);

        $request->validate([
            'order' => 'required|array',
            'order.*' => 'exists:persons,id'
        ]);

        $order = $request->input('order');
        foreach ($order as $index => $personId) {
            \App\Models\Person::where('id', $personId)
                ->where('territory_id', $territory->id)
                ->update(['sort_order' => $index]);
        }

        return response()->json(['success' => true]);
    }

    public function edit(Territory $territory)
    {
        $cities = City::hierarchical()->where('cities.is_active', true)->get();
        return view('territories.edit', compact('territory', 'cities'));
    }

    public function update(Request $request, Territory $territory)
    {
        $validated = $request->validate([
            'code' => [
                'required',
                'string',
                Rule::unique('territories', 'code')
                    ->ignore($territory->id)
                    ->whereNull('deleted_at')
            ],
            'city_id' => 'required|exists:cities,id',
            'neighborhood_name' => 'required|string|max:255',
            'status' => 'required|in:active,inactive',
            'notes' => 'nullable|string',
        ]);

        if ($request->code !== $territory->code) {
            // Cleanup any soft-deleted territory with the new code
            Territory::onlyTrashed()->where('code', $request->code)->forceDelete();
        }

        $territory->update($validated);

        if ($request->redirect_to === 'admin') {
            return redirect()->route('admin.territories.index')->with('success', 'Territorio actualizado exitosamente.');
        }

        return redirect()->route('territories.index')->with('success', 'Territorio actualizado exitosamente.');
    }

    public function destroy(Territory $territory)
    {
        if ($territory->persons()->exists()) {
            return back()->with('error', 'No se puede eliminar el territorio porque tiene personas registradas.');
        }

        if ($territory->assignments()->exists()) {
            return back()->with('error', 'No se puede eliminar el territorio porque tiene historial de asignaciones.');
        }

        $territory->delete();

        if (str_contains(url()->previous(), 'admin/territories')) {
            return redirect()->route('admin.territories.index')->with('success', 'Territorio eliminado exitosamente.');
        }

        return redirect()->route('territories.index')->with('success', 'Territorio eliminado exitosamente.');
    }
}
