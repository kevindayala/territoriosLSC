<?php

namespace App\Http\Controllers;

use App\Models\Territory;
use App\Models\City;
use Illuminate\Http\Request;

class AdminTerritoryController extends Controller
{
    /**
     * Display a listing of the resource (Admin Table View).
     */
    public function index(Request $request)
    {
        $query = Territory::with(['city', 'assignments.assignedTo']);

        // City Filter for Admin
        if ($request->filled('city_id') && $request->city_id !== 'todas') {
            $cityIds = \App\Models\City::where('id', $request->city_id)->orWhere('parent_id', $request->city_id)->pluck('id');
            $query->whereIn('city_id', $cityIds);
        }

        // Availability Filter for Admin
        if ($request->filled('filter')) {
            $filters = (array) $request->filter;

            $query->where(function ($q) use ($filters) {
                if (in_array('assigned', $filters)) {
                    $q->orWhereHas('assignments', function ($sq) {
                        $sq->whereNull('completed_at');
                    });
                }
                if (in_array('available', $filters)) {
                    $q->orWhereDoesntHave('assignments', function ($sq) {
                        $sq->whereNull('completed_at');
                    });
                }
                if (in_array('recommended', $filters)) {
                    $q->orWhere(function ($sq) {
                        $sq->whereDoesntHave('assignments', function ($sq2) {
                            $sq2->whereNull('completed_at');
                        })->where(function ($sq3) {
                            $sq3->whereNull('last_completed_at')
                                ->orWhere('last_completed_at', '<=', now()->subMonths(6));
                        });
                    });
                }
                if (in_array('never_completed', $filters)) {
                    $q->orWhereNull('last_completed_at');
                }
                if (in_array('completed_today', $filters)) {
                    $q->orWhereDate('last_completed_at', now()->toDateString())
                        ->orWhereHas('assignments', function ($aq) {
                            $aq->whereNull('completed_at')->whereDate('assigned_at', now()->toDateString());
                        });
                }
                if (in_array('completed_this_week', $filters)) {
                    $q->orWhere('last_completed_at', '>=', now()->startOfWeek())
                        ->orWhereHas('assignments', function ($aq) {
                            $aq->whereNull('completed_at')->where('assigned_at', '>=', now()->startOfWeek());
                        });
                }
                if (in_array('completed_this_month', $filters)) {
                    $q->orWhere('last_completed_at', '>=', now()->startOfMonth())
                        ->orWhereHas('assignments', function ($aq) {
                            $aq->whereNull('completed_at')->where('assigned_at', '>=', now()->startOfMonth());
                        });
                }
                if (in_array('completed_last_month', $filters)) {
                    $q->orWhere('last_completed_at', '>=', now()->subMonth())
                        ->orWhereHas('assignments', function ($aq) {
                            $aq->whereNull('completed_at')->where('assigned_at', '>=', now()->subMonth());
                        });
                }
            });
        }

        // Basic Search for Admin
        if ($request->filled('search')) {
            $search = $request->search;
            $searchWithoutSpaces = str_replace(' ', '', $search);

            $query->where(function ($q) use ($search, $searchWithoutSpaces) {
                $q->whereRaw("REPLACE(code, ' ', '') LIKE ?", ["%{$searchWithoutSpaces}%"])
                    ->orWhere('neighborhood_name', 'like', "%{$search}%")
                    ->orWhereHas('city', function ($cq) use ($search) {
                        $cq->where('name', 'like', "%{$search}%");
                    });
            });
        }

        // Sorting
        $sort = $request->input('sort', 'code');
        if ($sort === 'date_asc') {
            $query->orderByRaw('last_completed_at IS NULL DESC')
                ->orderBy('last_completed_at', 'ASC');
        } elseif ($sort === 'date_desc') {
            $query->withCount([
                'assignments as is_assigned_count' => function ($q) {
                    $q->whereNull('completed_at');
                }
            ])
                ->orderBy('is_assigned_count', 'DESC')
                ->orderByRaw('last_completed_at IS NULL ASC')
                ->orderBy('last_completed_at', 'DESC');
        } elseif ($sort === 'status') {
            $query->withCount([
                'assignments as is_assigned_count' => function ($q) {
                    $q->whereNull('completed_at');
                }
            ])
                ->orderBy('is_assigned_count', 'DESC')
                ->orderByRaw('LENGTH(code) ASC')
                ->orderBy('code', 'ASC');
        } else {
            $query->orderByRaw('LEFT(code, 1) ASC')
                ->orderByRaw('LENGTH(code) ASC')
                ->orderBy('code', 'ASC');
        }

        $territories = $query->paginate(50)
            ->withQueryString();

        $cities = City::hierarchical()->where('cities.is_active', true)->get();

        return view('admin.territories.index', compact('territories', 'cities'));
    }
}
