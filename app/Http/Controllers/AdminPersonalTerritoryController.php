<?php

namespace App\Http\Controllers;

use App\Models\Territory;
use App\Models\TerritoryAssignment;
use App\Models\User;
use Illuminate\Http\Request;

class AdminPersonalTerritoryController extends Controller
{
    public function index()
    {
        $activeAssignments = TerritoryAssignment::with(['territory.city', 'assignedTo'])
            ->where('type', 'personal')
            ->whereNull('completed_at')
            ->orderBy('due_date')
            ->get();

        $completedAssignments = TerritoryAssignment::with(['territory.city', 'assignedTo'])
            ->where('type', 'personal')
            ->whereNotNull('completed_at')
            ->orderByDesc('completed_at')
            ->get();

        return view('admin.personal-territories.index', compact('activeAssignments', 'completedAssignments'));
    }

    public function create()
    {
        // Get users with role 'publicador', or let's say all non-admin users or everyone
        $users = User::orderBy('name')->get();

        // Let's get active territories that are NOT currently assigned
        $territories = Territory::where('status', 'active')
            ->whereDoesntHave('assignments', function ($query) {
                $query->whereNull('completed_at');
            })
            ->with('city')
            ->get()
            ->sortBy('code', SORT_NATURAL);

        return view('admin.personal-territories.create', compact('users', 'territories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'territory_id' => 'required|exists:territories,id',
            'due_date' => 'nullable|date|after_or_equal:today',
        ], [
            'due_date.after_or_equal' => 'La fecha límite debe ser hoy o una fecha futura.'
        ]);

        $territory = Territory::findOrFail($request->territory_id);

        if ($territory->assignments()->whereNull('completed_at')->exists()) {
            return back()->with('error', 'Este territorio ya tiene una asignación activa.')->withInput();
        }

        TerritoryAssignment::create([
            'territory_id' => $territory->id,
            'assigned_to_user_id' => $request->user_id,
            'assigned_by_user_id' => auth()->id(),
            'assigned_at' => now(),
            'type' => 'personal',
            'due_date' => $request->due_date,
        ]);

        return redirect()->route('admin.personal-territories.index')
            ->with('success', 'Territorio personal asignado exitosamente.');
    }

    public function update(Request $request, TerritoryAssignment $personal_territory)
    {
        // We received $personal_territory which is the assignment
        $personal_territory->update([
            'completed_at' => now(),
        ]);

        $personal_territory->territory->update([
            'last_completed_at' => now(),
        ]);

        return back()->with('success', 'Asignación personal marcada como completada.');
    }

    public function destroy(TerritoryAssignment $personal_territory)
    {
        // For personal territories, admins are usually the ones deleting
        $territory = $personal_territory->territory;
        $wasCompleted = !is_null($personal_territory->completed_at);

        $personal_territory->delete();

        if ($wasCompleted) {
            $latestCompletion = $territory->assignments()
                ->whereNotNull('completed_at')
                ->latest('completed_at')
                ->first();

            $territory->update([
                'last_completed_at' => $latestCompletion ? $latestCompletion->completed_at : null
            ]);
        }

        return back()->with('success', 'Asignación personal eliminada correctamente.');
    }
}
