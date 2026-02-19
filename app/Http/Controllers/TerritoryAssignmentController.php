<?php

namespace App\Http\Controllers;

use App\Models\Territory;
use App\Models\TerritoryAssignment;
use Illuminate\Http\Request;

class TerritoryAssignmentController extends Controller
{
    public function index()
    {
        $activeAssignments = TerritoryAssignment::where('assigned_to_user_id', auth()->id())
            ->whereNull('completed_at')
            ->with(['territory.city'])
            ->orderBy('assigned_at')
            ->get();

        $historyAssignments = TerritoryAssignment::where('assigned_to_user_id', auth()->id())
            ->whereNotNull('completed_at')
            ->with(['territory'])
            ->latest('completed_at')
            ->take(10)
            ->get();

        return view('assignments.index', compact('activeAssignments', 'historyAssignments'));
    }

    public function store(Request $request)
    {
        $request->validate(['territory_id' => 'required|exists:territories,id']);

        $territory = Territory::findOrFail($request->territory_id);

        // Check if already assigned
        if ($territory->assignments()->whereNull('completed_at')->exists()) {
            return back()->with('error', 'Territorio ya asignado.');
        }

        // Restriction: Publicadores cannot self-assign
        if (!auth()->user()->hasRole(['admin', 'capitan'])) {
            return back()->with('error', 'No tienes permisos para asignarte territorios. Contacta a un administrador.');
        }

        TerritoryAssignment::create([
            'territory_id' => $territory->id,
            'assigned_to_user_id' => auth()->id(), // Logic for "Asignarme"
            'assigned_by_user_id' => auth()->id(),
            'assigned_at' => now(),
        ]);

        return back()->with('success', 'Territorio asignado exitosamente.');
    }

    public function update(Request $request, TerritoryAssignment $assignment)
    {
        // Add authorization check here if needed (e.g. only assignee or admin)
        if ($assignment->assigned_to_user_id !== auth()->id() && !auth()->user()->hasRole('admin')) {
            return back()->with('error', 'No autorizado.');
        }

        $assignment->update([
            'completed_at' => now(),
        ]);

        $assignment->territory->update([
            'last_completed_at' => now(),
        ]);

        return back()->with('success', 'Territorio marcado como completado.');
    }
    public function destroy(TerritoryAssignment $assignment)
    {
        if ($assignment->assigned_to_user_id !== auth()->id() && !auth()->user()->hasRole('admin')) {
            return back()->with('error', 'No autorizado.');
        }

        if ($assignment->completed_at) {
            return back()->with('error', 'No se puede cancelar una asignación completada.');
        }

        $assignment->delete();

        return back()->with('success', 'Asignación cancelada correctamente.');
    }
}
