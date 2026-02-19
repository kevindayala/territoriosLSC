<?php

namespace App\Http\Controllers;

use App\Models\Person;
use App\Models\Territory;
use Illuminate\Http\Request;

class PersonController extends Controller
{
    public function index(Request $request)
    {
        $query = Person::where('status', 'active')->with('territory');

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('full_name', 'like', '%' . $request->search . '%')
                    ->orWhere('address', 'like', '%' . $request->search . '%')
                    ->orWhereHas('territory', function ($q) use ($request) {
                        $q->where('code', 'like', '%' . $request->search . '%')
                            ->orWhere('neighborhood_name', 'like', '%' . $request->search . '%');
                    });
            });
        }

        if ($request->filled('city_id')) {
            $query->whereHas('territory', function ($q) use ($request) {
                $q->where('city_id', $request->city_id);
            });
        }

        $persons = $query->latest()->paginate(20);
        $cities = \App\Models\City::where('is_active', true)->orderBy('name')->get();

        return view('persons.index', compact('persons', 'cities'));
    }

    public function create()
    {
        $territories = Territory::where('status', 'active')->orderBy('code')->get();
        $cities = \App\Models\City::where('is_active', true)->orderBy('name')->get();
        return view('persons.create', compact('territories', 'cities'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'full_name' => 'required|string|max:255',
            'address' => 'required|string',
            'territory_id' => 'required|exists:territories,id',
            'map_url' => 'nullable|url',
            'notes' => 'nullable|string',
        ]);

        $isAdmin = auth()->user()->hasRole('admin');

        Person::create([
            'full_name' => $request->full_name,
            'address' => $request->address,
            'territory_id' => $request->territory_id,
            'map_url' => $request->map_url,
            'notes' => $request->notes,
            'status' => $isAdmin ? 'active' : 'pending',
            'created_by_user_id' => auth()->id(),
            'approved_at' => $isAdmin ? now() : null,
            'approved_by_user_id' => $isAdmin ? auth()->id() : null,
        ]);

        $message = $isAdmin
            ? 'Persona registrada y aprobada automáticamente.'
            : 'Persona registrada, pendiente de aprobación.';

        return redirect()->route('persons.index')->with('success', $message);
    }

    public function edit(Person $person)
    {
        $territories = Territory::where('status', 'active')->orderBy('code')->get();
        $cities = \App\Models\City::where('is_active', true)->orderBy('name')->get();
        return view('persons.edit', compact('person', 'territories', 'cities'));
    }

    public function update(Request $request, Person $person)
    {
        // Approve Logic (if simple approve button pressed)
        if ($request->has('approve') && auth()->user()->hasRole('admin')) {
            $person->update([
                'status' => 'active',
                'approved_at' => now(),
                'approved_by_user_id' => auth()->id(),
            ]);
            return back()->with('success', 'Persona aprobada.');
        }

        // Full Edit Logic
        $request->validate([
            'full_name' => 'required|string|max:255',
            'address' => 'required|string',
            'territory_id' => 'required|exists:territories,id',
            'status' => 'required|in:active,inactive,pending',
            'map_url' => 'nullable|url',
            'notes' => 'nullable|string',
        ]);

        $data = $request->only(['full_name', 'address', 'territory_id', 'map_url', 'notes', 'status']);

        if (auth()->user()->hasRole('admin')) {
            // Force status to 'active' and set approval info if it was pending or had pending changes
            $wasPending = $person->status === 'pending' || !is_null($person->pending_changes);

            $data['status'] = $request->status;

            if ($wasPending && $request->status !== 'inactive') {
                $data['status'] = 'active';
                $data['approved_at'] = now();
                $data['approved_by_user_id'] = auth()->id();
            }
            // Clear any pending state when admin saves the person
            $data['pending_changes'] = null;
            $data['pending_by_user_id'] = null;

            $person->update($data);
        } else {
            // Non-admin logic
            if ($person->status === 'active') {
                // If there are already pending changes...
                if (!is_null($person->pending_changes)) {
                    // ...and they were NOT made by the current user, block editing.
                    if ($person->pending_by_user_id !== auth()->id()) {
                        return back()->with('error', 'Esta persona ya tiene cambios pendientes de revisión por parte de otro usuario.');
                    }
                }

                // If it's already active, don't overwrite the live data.
                // Store changes in pending_changes column and track WHO did it.
                $person->update([
                    'pending_changes' => $data,
                    'pending_by_user_id' => auth()->id()
                ]);
            } else {
                // If it's already pending (a new record not yet public), 
                // just overwrite it directly.
                $person->update($data);
            }
        }

        $msg = auth()->user()->hasRole('admin') ? 'Persona actualizada.' : 'Cambios guardados. Se requiere aprobación de un administrador.';

        if ($request->redirect_to === 'approvals') {
            return redirect()->route('approvals.index')->with('success', $msg);
        }

        return redirect()->route('persons.index')->with('success', $msg);
    }


}
