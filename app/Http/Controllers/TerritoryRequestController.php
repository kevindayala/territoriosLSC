<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TerritoryRequestController extends Controller
{
    public function create()
    {
        $territories = \App\Models\Territory::where('status', 'active')
            ->orderByRaw('LENGTH(code) ASC')
            ->orderBy('code', 'asc')
            ->get();

        return view('territory-requests.create', compact('territories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'territory_id' => 'required|exists:territories,id',
            'expected_return_date' => 'required|date|after_or_equal:today',
        ]);

        \App\Models\TerritoryRequest::create([
            'user_id' => auth()->id(),
            'territory_id' => $request->territory_id,
            'expected_return_date' => $request->expected_return_date,
            'status' => 'pending',
        ]);

        return redirect()->route('dashboard')->with('success', '¡Su solicitud ha sido enviada!');
    }
}
