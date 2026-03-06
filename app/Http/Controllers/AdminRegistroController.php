<?php

namespace App\Http\Controllers;

use App\Models\TerritoryAssignment;
use App\Models\Territory;
use App\Models\User;
use App\Models\City;
use Illuminate\Http\Request;

class AdminRegistroController extends Controller
{
    public function index(Request $request)
    {
        $query = TerritoryAssignment::with(['territory.city', 'assignedTo', 'assignedBy']);

        // Filtro por Ciudad
        if ($request->filled('city_id') && $request->city_id !== 'todas') {
            $cityIds = City::where('id', $request->city_id)->orWhere('parent_id', $request->city_id)->pluck('id');
            $query->whereHas('territory', function ($q) use ($cityIds) {
                $q->whereIn('city_id', $cityIds);
            });
        }

        // Filtros de estado / recomendación (basados en el territorio vinculado)
        if ($request->filled('filter')) {
            $filters = (array) $request->filter;
            $query->whereHas('territory', function ($q) use ($filters) {
                $q->where(function ($sq) use ($filters) {
                    if (in_array('assigned', $filters)) {
                        $sq->orWhereHas('assignments', function ($ssq) {
                            $ssq->whereNull('completed_at');
                        });
                    }
                    if (in_array('available', $filters)) {
                        $sq->orWhereDoesntHave('assignments', function ($ssq) {
                            $ssq->whereNull('completed_at');
                        });
                    }
                    if (in_array('completed_last_month', $filters)) {
                        $sq->orWhere('last_completed_at', '>=', now()->subMonth());
                    }
                });
            });
        }

        // Búsqueda
        if ($request->filled('search')) {
            $search = $request->search;
            $searchWithoutSpaces = str_replace(' ', '', $search);
            $query->where(function ($q) use ($search, $searchWithoutSpaces) {
                $q->whereHas('territory', function ($q) use ($search, $searchWithoutSpaces) {
                    $q->whereRaw("REPLACE(code, ' ', '') LIKE ?", ["%{$searchWithoutSpaces}%"]);
                })->orWhereHas('assignedTo', function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%");
                });
            });
        }

        // Ordenar
        $sort = $request->input('sort', 'id');
        if ($sort === 'code') {
            $query->join('territories', 'territory_assignments.territory_id', '=', 'territories.id')
                ->select('territory_assignments.*')
                ->orderByRaw('LEFT(territories.code, 1) ASC')
                ->orderByRaw('LENGTH(territories.code) ASC')
                ->orderBy('territories.code', 'ASC');
        } elseif ($sort === 'date_desc') {
            $query->orderBy('assigned_at', 'desc');
        } elseif ($sort === 'date_asc') {
            $query->orderBy('assigned_at', 'asc');
        } else {
            $query->orderBy('id', 'desc');
        }

        $registros = $query->paginate(20)->withQueryString();
        $cities = City::hierarchical()->where('cities.is_active', true)->get();

        return view('admin.registros.index', compact('registros', 'cities'));
    }

    public function create()
    {
        // Solo mostrar territorios que NO tienen asignación activa
        $territories = Territory::whereDoesntHave('assignments', function ($q) {
            $q->whereNull('completed_at');
        })->orderByRaw('LENGTH(code) ASC')
            ->orderBy('code', 'asc')
            ->get();

        $users = User::orderBy('name')->get();
        return view('admin.registros.create', compact('territories', 'users'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'territory_id' => 'required|exists:territories,id',
            'assigned_to_user_id' => 'required|exists:users,id',
            'assigned_at' => 'required|date',
            'completed_at' => 'nullable|date',
            'type' => 'required|in:regular,personal',
        ]);

        // Validar que el territorio no esté ocupado si es una asignación activa
        if (!$request->completed_at) {
            $exists = TerritoryAssignment::where('territory_id', $request->territory_id)
                ->whereNull('completed_at')
                ->exists();
            if ($exists) {
                return back()->with('error', 'Este territorio ya tiene una asignación activa.')->withInput();
            }
        }

        $assignment = TerritoryAssignment::create([
            'territory_id' => $request->territory_id,
            'assigned_to_user_id' => $request->assigned_to_user_id,
            'assigned_by_user_id' => auth()->id(),
            'assigned_at' => $request->assigned_at,
            'completed_at' => $request->completed_at,
            'type' => $request->type,
        ]);

        if ($request->completed_at) {
            $assignment->territory->update([
                'last_completed_at' => $request->completed_at,
            ]);
        }

        return redirect()->route('admin.registros.index')->with('success', 'Registro creado correctamente.');
    }

    public function edit(TerritoryAssignment $registro)
    {
        // En edición mostramos el actual + los disponibles
        $territories = Territory::whereDoesntHave('assignments', function ($q) use ($registro) {
            $q->whereNull('completed_at')->where('id', '!=', $registro->id);
        })->orderByRaw('LENGTH(code) ASC')
            ->orderBy('code', 'asc')
            ->get();

        $users = User::orderBy('name')->get();
        return view('admin.registros.edit', compact('registro', 'territories', 'users'));
    }

    public function update(Request $request, TerritoryAssignment $registro)
    {
        $request->validate([
            'territory_id' => 'required|exists:territories,id',
            'assigned_to_user_id' => 'required|exists:users,id',
            'assigned_at' => 'required|date',
            'completed_at' => 'nullable|date',
            'type' => 'required|in:regular,personal',
        ]);

        // Validar que el territorio no esté ocupado por OTRO registro activo
        if (!$request->completed_at) {
            $exists = TerritoryAssignment::where('territory_id', $request->territory_id)
                ->whereNull('completed_at')
                ->where('id', '!=', $registro->id)
                ->exists();
            if ($exists) {
                return back()->with('error', 'Este territorio ya tiene otra asignación activa.')->withInput();
            }
        }

        $registro->update([
            'territory_id' => $request->territory_id,
            'assigned_to_user_id' => $request->assigned_to_user_id,
            'assigned_at' => $request->assigned_at,
            'completed_at' => $request->completed_at,
            'type' => $request->type,
        ]);

        if ($request->completed_at) {
            $registro->territory->update([
                'last_completed_at' => $request->completed_at,
            ]);
        } else {
            // Recalculatar last_completed_at si se quito la fecha
            $latestCompletion = $registro->territory->assignments()
                ->whereNotNull('completed_at')
                ->latest('completed_at')
                ->first();

            $registro->territory->update([
                'last_completed_at' => $latestCompletion ? $latestCompletion->completed_at : null
            ]);
        }

        return redirect()->route('admin.registros.index')->with('success', 'Registro actualizado correctamente.');
    }

    public function destroy(TerritoryAssignment $registro)
    {
        $territory = $registro->territory;
        $wasCompleted = !is_null($registro->completed_at);

        $registro->delete();

        if ($wasCompleted && $territory) {
            $latestCompletion = $territory->assignments()
                ->whereNotNull('completed_at')
                ->latest('completed_at')
                ->first();

            $territory->update([
                'last_completed_at' => $latestCompletion ? $latestCompletion->completed_at : null
            ]);
        }

        return redirect()->route('admin.registros.index')->with('success', 'Registro eliminado correctamente.');
    }
}
