<?php

namespace App\Http\Controllers;

use App\Models\Person;
use App\Models\City;
use App\Models\Territory;
use Illuminate\Http\Request;

class InactivePersonController extends Controller
{
    public function index(Request $request)
    {
        $query = Person::where('status', 'inactive');

        // Apply filters (same as main persons list)
        if ($request->filled('search')) {
            $query->where('full_name', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('city_id')) {
            $query->whereHas('territory', function ($q) use ($request) {
                $q->where('city_id', $request->city_id);
            });
        }

        if ($request->filled('territory_id')) {
            $query->where('territory_id', $request->territory_id);
        }

        $persons = $query->with(['territory.city'])->latest()->paginate(10);
        $territories = Territory::all();
        $cities = City::all();

        return view('persons.inactive', compact('persons', 'territories', 'cities'));
    }

    public function activate(Person $person)
    {
        $person->update([
            'status' => 'active',
            'approved_at' => now(),
            'approved_by_user_id' => auth()->id()
        ]);

        return redirect()->route('admin.persons.inactive')->with('success', 'Persona activada correctamente.');
    }
}
