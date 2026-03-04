<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AdminSettingsController extends Controller
{
    public function index()
    {
        $publicRegistration = \App\Models\Setting::get('public_registration', 'true') === 'true';
        return view('admin.settings.index', compact('publicRegistration'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'public_registration' => 'required|string|in:true,false',
        ]);

        \App\Models\Setting::set('public_registration', $request->public_registration);

        return back()->with('success', 'Configuración actualizada correctamente.');
    }
}
