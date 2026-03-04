<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Role;

class AdminUserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = User::with('roles');

        if ($request->filter === 'trashed') {
            $query->onlyTrashed();
        } elseif ($request->filter === 'active') {
            $query->where('is_active', true);
        } elseif ($request->filter === 'inactive') {
            $query->where('is_active', false);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $users = $query->paginate(10);
        return view('admin.users.index', compact('users'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $roles = Role::all();
        return view('admin.users.create', compact('roles'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
                Rule::unique('users')->whereNull('deleted_at')
            ],
            'role' => ['required', 'exists:roles,name'],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make(\Illuminate\Support\Str::random(16)), // Random password
            'is_active' => true,
        ]);

        $user->assignRole($request->role);

        // Send reset link using the Password facade
        \Illuminate\Support\Facades\Password::sendResetLink($request->only('email'));

        return redirect()->route('users.index')->with('success', 'Usuario creado. Se ha enviado un correo para configurar la contraseña.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        $roles = Role::all();
        return view('admin.users.edit', compact('user', 'roles'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
                Rule::unique('users')->ignore($user->id)->whereNull('deleted_at')
            ],
            'role' => ['required', 'exists:roles,name'],
            'password' => ['nullable', 'confirmed', Rules\Password::defaults()],
            'is_active' => ['required', 'boolean'],
        ]);

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'is_active' => $request->is_active,
        ]);

        if ($request->filled('password')) {
            $user->update([
                'password' => Hash::make($request->password),
            ]);
        }

        $user->syncRoles($request->role);

        return redirect()->route('users.index')->with('success', 'Usuario actualizado exitosamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'No puedes eliminar tu propia cuenta.');
        }

        // Check for dependencies: created persons, approved persons, assignments
        if (\App\Models\Person::where('created_by_user_id', $user->id)->exists()) {
            return back()->with('error', 'No se puede eliminar el usuario porque ha registrado personas.');
        }

        if (\App\Models\Person::where('approved_by_user_id', $user->id)->exists()) {
            return back()->with('error', 'No se puede eliminar el usuario porque es responsable de la aprobación de personas.');
        }

        if (
            \App\Models\TerritoryAssignment::where('assigned_by_user_id', $user->id)
                ->orWhere('assigned_to_user_id', $user->id)->exists()
        ) {
            return back()->with('error', 'No se puede eliminar el usuario porque tiene asignaciones de territorio vinculadas.');
        }

        $user->delete();

        return redirect()->route('users.index')->with('success', 'Usuario eliminado exitosamente.');
    }

    /**
     * Restore the specified soft-deleted resource.
     */
    public function restore($id)
    {
        $user = User::onlyTrashed()->findOrFail($id);
        $user->restore();

        return redirect()->back()->with('success', 'Usuario restaurado exitosamente.');
    }
}
