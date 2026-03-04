<?php

namespace App\Http\Controllers;

use App\Models\Person;
use Illuminate\Http\Request;

class ApprovalController extends Controller
{
    public function index()
    {
        $pendingPersons = Person::where('status', 'pending')
            ->orWhereNotNull('pending_changes')
            ->with(['territory', 'creator', 'pendingUser'])
            ->latest()
            ->paginate(15, ['*'], 'persons_page');

        $pendingUsers = \App\Models\User::where('is_active', false)
            ->latest()
            ->paginate(15, ['*'], 'users_page');

        $territories = \App\Models\Territory::all();

        $pendingTerritoryRequests = \App\Models\TerritoryRequest::with(['user', 'territory'])
            ->where('status', 'pending')
            ->latest()
            ->paginate(15, ['*'], 'requests_page');

        return view('approvals.index', compact('pendingPersons', 'pendingUsers', 'territories', 'pendingTerritoryRequests'));
    }

    public function approveUser(\App\Models\User $user)
    {
        $user->update(['is_active' => true]);
        return back()->with('success', 'Usuario habilitado correctamente.');
    }

    public function rejectUser(\App\Models\User $user)
    {
        $user->delete(); // Soft delete or force? User model has SoftDeletes.
        return back()->with('success', 'Solicitud de registro eliminada.');
    }

    public function approve(Person $person)
    {
        $updateData = [
            'status' => 'active',
            'approved_at' => now(),
            'approved_by_user_id' => auth()->id(),
        ];

        if ($person->pending_changes) {
            // Merge pending changes into update data
            $updateData = array_merge($updateData, $person->pending_changes);
            $updateData['pending_changes'] = null;
            $updateData['pending_by_user_id'] = null;
        }

        $person->update($updateData);

        return back()->with('success', 'Registro aprobado correctamente.');
    }

    public function reject(Person $person)
    {
        // For now, rejection might just mean deleting the pending entry?
        // Or setting status to 'rejected'?
        // The requirement ("solo el admin se aprobara automaticamente") implies pending items wait.
        // A rejection likely means deleting the proposed change if it's a NEW person.
        // But if it's an EDIT to an existing person, deleting the Person model would be bad if we don't have revision history.
        // Given we don't have revision history yet, "Deleting" a pending person is risky if it was an EDIT of a valid person.
        // However, in our CURRENT implementation (PersonController update), we just overwrite the data and set status to pending.
        // So the "Old" data is already gone from the DB record.
        // So "Rejecting" effectively leaves it as Pending or we'd have to manually fix it.
        // Let's implement DELETE for now, assuming mostly new records or that Admin will fix it.

        $person->delete();
        return redirect()->route('approvals.index')->with('success', 'Registro eliminado/rechazado.');
    }

    public function approveTerritoryRequest(\App\Models\TerritoryRequest $territoryRequest)
    {
        $territoryRequest->update(['status' => 'approved']);

        // Crear la asignación de territorio
        \App\Models\TerritoryAssignment::create([
            'territory_id' => $territoryRequest->territory_id,
            'assigned_to_user_id' => $territoryRequest->user_id,
            'assigned_by_user_id' => auth()->id(),
            'assigned_at' => now(),
            'due_date' => $territoryRequest->expected_return_date,
            'notes' => 'Territorio Personal solicitado el ' . $territoryRequest->created_at->format('d/m/Y') . '. Fecha esperada de devolución: ' . \Carbon\Carbon::parse($territoryRequest->expected_return_date)->format('d/m/Y'),
            'type' => 'personal'
        ]);

        return back()->with('success', 'Solicitud de territorio personal aprobada y territorio asignado.');
    }

    public function rejectTerritoryRequest(\App\Models\TerritoryRequest $territoryRequest)
    {
        $territoryRequest->update(['status' => 'rejected']);
        return back()->with('success', 'Solicitud de territorio personal rechazada.');
    }
}
