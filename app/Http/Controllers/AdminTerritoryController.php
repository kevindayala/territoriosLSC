<?php

namespace App\Http\Controllers;

use App\Models\Territory;
use Illuminate\Http\Request;

class AdminTerritoryController extends Controller
{
    /**
     * Display a listing of the resource (Admin Table View).
     */
    public function index(Request $request)
    {
        $query = Territory::with(['city', 'assignments.assignedTo']);

        // Basic Search for Admin
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('code', 'like', "%{$search}%")
                ->orWhere('neighborhood_name', 'like', "%{$search}%")
                ->orWhereHas('city', function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%");
                });
        }

        $territories = $query->orderBy('code')->paginate(20)->withQueryString();

        return view('admin.territories.index', compact('territories'));
    }
}
