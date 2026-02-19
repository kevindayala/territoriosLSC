<?php

namespace App\Http\Controllers;

use App\Models\City;
use Illuminate\Http\Request;

class CityController extends Controller
{
    public function index()
    {
        $cities = City::withCount(['territories'])->get();
        return view('cities.index', compact('cities'));
    }

    public function create()
    {
        return view('cities.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'is_active' => 'boolean',
        ]);

        $validated['slug'] = \Illuminate\Support\Str::slug($validated['name']);

        City::create($validated);

        return redirect()->route('cities.index')->with('success', 'Ciudad creada exitosamente.');
    }

    public function edit(City $city)
    {
        return view('cities.edit', compact('city'));
    }

    public function update(Request $request, City $city)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'is_active' => 'boolean',
        ]);

        $validated['slug'] = \Illuminate\Support\Str::slug($validated['name']);

        $city->update($validated);

        return redirect()->route('cities.index')->with('success', 'Ciudad actualizada exitosamente.');
    }

    public function destroy(City $city)
    {
        // Check for dependencies
        if ($city->territories()->exists()) {
            return back()->with('error', 'No se puede eliminar la ciudad porque tiene territorios asociados.');
        }

        $city->delete();

        return redirect()->route('cities.index')->with('success', 'Ciudad eliminada exitosamente.');
    }
}
