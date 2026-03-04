<x-app-layout>
    <x-slot name="title">Editar Usuario</x-slot>
    <x-slot name="logo_url">{{ route('users.index') }}</x-slot>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Editar Usuario') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <form action="{{ route('users.update', $user) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="grid grid-cols-1 gap-6">
                            {{-- Name --}}
                            <div>
                                <label for="name"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300">Nombre</label>
                                <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}" required
                                    class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                @error('name')
                                    <span class="text-red-500 text-sm">{{ $message }}</span>
                                @enderror
                            </div>

                            {{-- Email --}}
                            <div>
                                <label for="email"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300">Correo
                                    Electrónico</label>
                                <input type="email" name="email" id="email" value="{{ old('email', $user->email) }}"
                                    required
                                    class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                @error('email')
                                    <span class="text-red-500 text-sm">{{ $message }}</span>
                                @enderror
                            </div>

                            {{-- Password (Optional) --}}
                            <div>
                                <label for="password"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300">Contraseña (Dejar
                                    en blanco para mantener actual)</label>
                                <input type="password" name="password" id="password"
                                    class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                @error('password')
                                    <span class="text-red-500 text-sm">{{ $message }}</span>
                                @enderror
                            </div>

                            {{-- Confirm Password --}}
                            <div>
                                <label for="password_confirmation"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300">Confirmar
                                    Contraseña</label>
                                <input type="password" name="password_confirmation" id="password_confirmation"
                                    class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                            </div>

                            {{-- Status --}}
                            <div>
                                <label for="is_active"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300">Estado de la
                                    cuenta</label>
                                <select name="is_active" id="is_active" required
                                    class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                    <option value="1" {{ old('is_active', $user->is_active) == 1 ? 'selected' : '' }}>
                                        Activo</option>
                                    <option value="0" {{ old('is_active', $user->is_active) == 0 ? 'selected' : '' }}>
                                        Inactivo (Acceso restringido)</option>
                                </select>
                                @error('is_active')
                                    <span class="text-red-500 text-sm">{{ $message }}</span>
                                @enderror
                            </div>

                            {{-- Role --}}
                            <div>
                                <label for="role"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300">Rol</label>
                                <select name="role" id="role" required
                                    class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                    <option value="">Seleccione un Rol</option>
                                    @foreach($roles as $role)
                                        <option value="{{ $role->name }}" {{ (old('role') == $role->name || $user->hasRole($role->name)) ? 'selected' : '' }}>{{ ucfirst($role->name) }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('role')
                                    <span class="text-red-500 text-sm">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="flex items-center gap-4">
                                <button type="submit"
                                    class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                    Actualizar
                                </button>
                                <a href="{{ route('users.index') }}"
                                    class="text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-gray-100">Cancelar</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>