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
        $query = Territory::withCount([
            'persons',
            'assignments as annual_completions_count' => function ($query) {
                // Service Year starts on September 1st
                $startYear = now()->month >= 9 ? now()->year : now()->year - 1;
                $startDate = \Carbon\Carbon::create($startYear, 9, 1)->startOfDay();

                $query->whereNotNull('completed_at')->where('completed_at', '>=', $startDate);
            }
        ])->with([
                    'city',
                    'assignments' => function ($q) {
                        $q->latest('id')->limit(1);
                    }
                ]);

        if ($request->filled('city_id')) {
            $query->where('territories.city_id', $request->city_id);
        }

        if ($request->filled('assignment_status')) {
            if ($request->assignment_status === 'assigned') {
                $query->whereHas('assignments', function ($q) {
                    $q->whereNull('completed_at');
                });
            } elseif ($request->assignment_status === 'available') {
                $query->whereDoesntHave('assignments', function ($q) {
                    $q->whereNull('completed_at');
                });
            }
        }

        if ($request->filled('status')) {
            $query->where('territories.status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('territories.code', 'like', "%{$search}%")
                    ->orWhere('territories.neighborhood_name', 'like', "%{$search}%");
            });
        }

        // Sorting logic
        $sortBy = $request->input('sort_by', 'code');
        $sortOrder = $request->input('sort_order', 'asc');

        if ($sortBy === 'name') {
            $query->orderBy('neighborhood_name', $sortOrder);
        } elseif ($sortBy === 'last_completed_at') {
            $query->orderBy('last_completed_at', $sortOrder);
        } else {
            $query->orderBy('territories.code', $sortOrder);
        }

        $territories = $query->paginate(20)->withQueryString();

        $cities = City::where('is_active', true)->get();

        return view('territories.index', compact('territories', 'cities'));
    }

    public function create()
    {
        $cities = City::where('is_active', true)->get();
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

    public function edit(Territory $territory)
    {
        $cities = City::where('is_active', true)->get();
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
