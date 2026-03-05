<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TerritoryRequestController extends Controller
{
    public function create()
    {
        // Solo territorios activos que NO están asignados actualmente
        $territories = \App\Models\Territory::with('city')
            ->where('status', 'active')
            ->whereDoesntHave('assignments', function ($q) {
                $q->whereNull('completed_at');
            })
            ->get()
            ->sortBy('code', SORT_NATURAL);

        return view('territory-requests.create', compact('territories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'territory_id' => 'required|exists:territories,id',
            'expected_return_date' => 'required|date|after_or_equal:today',
        ]);

        // Validar que el territorio no esté ocupado
        $isOccupied = \App\Models\TerritoryAssignment::where('territory_id', $request->territory_id)
            ->whereNull('completed_at')
            ->exists();

        if ($isOccupied) {
            return back()->with('error', 'Lo sentimos, este territorio acaba de ser asignado.')->withInput();
        }

        \App\Models\TerritoryRequest::create([
            'user_id' => auth()->id(),
            'territory_id' => $request->territory_id,
            'expected_return_date' => $request->expected_return_date,
            'status' => 'pending',
        ]);

        return redirect()->route('dashboard')->with('success', '¡Su solicitud ha sido enviada!');
    }
}
