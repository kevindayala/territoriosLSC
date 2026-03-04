<?php

namespace App\Http\Controllers;

use App\Models\City;
use Illuminate\Http\Request;

class CityController extends Controller
{
    public function index()
    {
        // Load parent relationship and count territories, applying hierarchical sorting
        $cities = City::hierarchical()->with(['parent'])->withCount(['territories'])->get();
        return view('cities.index', compact('cities'));
    }

    public function create()
    {
        $parentCities = City::whereNull('parent_id')->get();
        return view('cities.create', compact('parentCities'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'parent_id' => 'nullable|exists:cities,id',
            'name' => 'required|string|max:255',
            'is_active' => 'boolean',
        ]);

        $validated['slug'] = \Illuminate\Support\Str::slug($validated['name']);

        City::create($validated);

        return redirect()->route('cities.index')->with('success', 'Ciudad creada exitosamente.');
    }

    public function edit(City $city)
    {
        $parentCities = City::whereNull('parent_id')->where('id', '!=', $city->id)->get();
        return view('cities.edit', compact('city', 'parentCities'));
    }

    public function update(Request $request, City $city)
    {
        $validated = $request->validate([
            'parent_id' => 'nullable|exists:cities,id',
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
