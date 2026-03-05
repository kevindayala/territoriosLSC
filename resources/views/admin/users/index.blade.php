<x-app-layout>
    <x-slot name="title">Usuarios</x-slot>
    <x-slot name="logo_url">{{ route('admin.settings') }}</x-slot>

    <div class="pt-6 pb-12 md:py-12" x-data="{ selectedId: null }">
        {{-- Título de la página --}}
        <x-slot name="header">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4" x-data="{}">
                <h2
                    class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight text-center sm:text-left">
                    {{ __('Usuarios') }}
                </h2>
                <div class="flex items-center flex-wrap sm:flex-nowrap gap-2 w-full sm:w-auto">
                    <form action="{{ route('users.toggle-registration') }}" method="POST" class="flex-1 sm:flex-none">
                        @csrf
                        <button type="submit"
                            title="{{ $publicRegistration ? 'Click para desactivar' : 'Click para activar' }}"
                            class="w-full inline-flex items-center justify-center px-3 h-11 sm:h-10 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-full shadow-sm hover:bg-gray-50 dark:hover:bg-gray-700 active:scale-95 transition-all gap-2">
                            <div
                                class="w-2 h-2 rounded-full {{ $publicRegistration ? 'bg-green-500 animate-pulse' : 'bg-red-500' }}">
                            </div>
                            <span class="text-[13px] sm:text-sm font-bold whitespace-nowrap">
                                {{ __('Permitir registrarse') }}
                            </span>
                        </button>
                    </form>

                    <a href="{{ route('users.create') }}"
                        class="flex-1 sm:flex-none inline-flex items-center justify-center px-4 h-11 sm:h-10 bg-blue-600 hover:bg-blue-700 text-white rounded-full shadow-lg shadow-blue-500/20 active:scale-95 transition-all gap-2">
                        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 4v16m8-8H4" />
                        </svg>
                        <span class="text-[13px] sm:text-sm font-bold whitespace-nowrap hidden sm:inline">Nuevo
                            Usuario</span>
                        <span class="text-[13px] sm:text-sm font-bold whitespace-nowrap sm:hidden">Nuevo</span>
                    </a>
                </div>
            </div>
        </x-slot>

        {{-- Top Navigation & Search --}}
        <div class="mt-4 sm:mt-6 mb-8">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <form method="GET" action="{{ route('users.index') }}" class="w-full">
                    <div
                        class="border-b border-gray-200 dark:border-gray-700 pb-3 mb-2 flex flex-col sm:flex-row items-center justify-between gap-4">
                        <div class="flex items-center w-full">
                            <input type="text" name="search" value="{{ request('search') }}"
                                class="flex-1 border border-gray-400 dark:border-gray-600 rounded bg-white dark:bg-gray-800 focus:ring-1 focus:ring-gray-500 focus:border-gray-500 text-[15px] placeholder-gray-500 dark:placeholder-gray-400 text-gray-800 dark:text-gray-200 px-3 py-1.5 outline-none shadow-sm"
                                placeholder="{{ __('Buscar por nombre o email...') }}">
                            <div class="flex items-center gap-3 pl-3">
                                <button type="submit"
                                    class="text-black dark:text-gray-200 hover:text-gray-700 dark:hover:text-white transition-colors focus:outline-none flex-shrink-0">
                                    <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                        stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                        <circle cx="11" cy="11" r="8"></circle>
                                        <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                                    </svg>
                                </button>
                            </div>
                        </div>

                        <div class="w-full sm:w-auto flex-shrink-0">
                            <select name="filter" onchange="this.form.submit()"
                                class="w-full sm:w-auto border border-gray-400 dark:border-gray-600 rounded bg-white dark:bg-gray-800 focus:ring-1 focus:ring-gray-500 focus:border-gray-500 text-[14px] text-gray-800 dark:text-gray-200 pl-3 pr-8 py-1.5 outline-none shadow-sm cursor-pointer">
                                <option value="">Todos los usuarios</option>
                                <option value="active" {{ request('filter') == 'active' ? 'selected' : '' }}>
                                    {{ __('Activos') }}
                                </option>
                                <option value="inactive" {{ request('filter') == 'inactive' ? 'selected' : '' }}>
                                    {{ __('Inactivos') }}
                                </option>
                                <option value="trashed" {{ request('filter') == 'trashed' ? 'selected' : '' }}>
                                    {{ __('Eliminados') }}
                                </option>
                            </select>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        {{-- Contenido Principal --}}
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Mobile Card View -->
            <div class="block md:hidden w-full space-y-4">
                @forelse($users as $user)
                    <div
                        class="bg-white dark:bg-gray-800 p-5 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm relative">
                        <div class="flex items-start gap-4 mb-5">
                            <img src="{{ $user->profile_photo_url }}" alt="{{ $user->name }}"
                                class="h-12 w-12 rounded-full object-cover border border-gray-200 dark:border-gray-600 shadow-sm shrink-0">
                            <div class="flex-1 min-w-0">
                                <h3
                                    class="font-bold text-gray-900 dark:text-white text-[15px] leading-tight flex items-center gap-2 truncate">
                                    {{ $user->name }}
                                </h3>
                                <p class="text-[13px] text-gray-500 dark:text-gray-400 mt-0.5 truncate">
                                    {{ $user->email }}
                                </p>
                                <div class="mt-2.5 flex flex-wrap gap-1.5">
                                    @foreach($user->roles as $role)
                                        <span
                                            class="px-2 py-[3px] text-[9px] uppercase font-black tracking-wider rounded bg-slate-100 text-slate-700 dark:bg-slate-700 dark:text-slate-300">
                                            {{ $role->name }}
                                        </span>
                                    @endforeach
                                </div>
                            </div>
                            <div class="flex flex-col items-end shrink-0">
                                @if($user->trashed())
                                    <span
                                        class="px-2 py-1 text-[10px] font-black uppercase tracking-wider text-red-600 dark:text-red-400 bg-red-50 dark:bg-red-900/20 rounded-md">Eliminado</span>
                                @elseif(!$user->is_active)
                                    <span
                                        class="px-2 py-1 text-[10px] font-black uppercase tracking-wider text-yellow-600 dark:text-yellow-400 bg-yellow-50 dark:bg-yellow-900/20 rounded-md">Inactivo</span>
                                @else
                                    <span
                                        class="px-2 py-1 text-[10px] font-black uppercase tracking-wider text-green-600 dark:text-green-400 bg-green-50 dark:bg-green-900/20 rounded-md">Activo</span>
                                @endif
                            </div>
                        </div>
                        <div class="flex gap-2 mt-4 pt-4 border-t border-gray-100 dark:border-gray-700/50">
                            @if($user->trashed())
                                <form action="{{ route('users.restore', $user->id) }}" method="POST" class="flex-1">
                                    @csrf
                                    <button type="submit"
                                        class="w-full flex items-center justify-center px-4 py-2 bg-emerald-50 hover:bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:hover:bg-emerald-900/50 dark:text-emerald-400 text-sm font-bold rounded-xl transition-colors border border-emerald-100 dark:border-emerald-800/50">
                                        <svg class="w-4 h-4 me-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                        </svg>
                                        Restaurar
                                    </button>
                                </form>
                            @else
                                <a href="{{ route('users.edit', $user) }}"
                                    class="flex-1 flex items-center justify-center px-4 py-2 bg-white hover:bg-gray-50 text-gray-700 dark:bg-gray-800 dark:hover:bg-gray-700/50 dark:text-gray-300 text-sm font-bold rounded-xl transition-colors border border-gray-200 dark:border-gray-700">
                                    <svg class="w-4 h-4 me-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                    Editar
                                </a>
                                <form id="delete-form-mobile-{{ $user->id }}" action="{{ route('users.destroy', $user) }}"
                                    method="POST" class="inline-block">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button"
                                        @click="selectedId = {{ $user->id }}; $dispatch('open-modal', 'confirm-delete-mobile')"
                                        class="p-2 bg-red-50 dark:bg-red-900/20 text-red-600 border border-red-100 dark:border-red-900/30 rounded-xl transition-colors">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                @empty
                    <div
                        class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-sm text-center text-gray-500 dark:text-gray-400 border border-gray-100 dark:border-gray-700">
                        No hay usuarios registrados.
                    </div>
                @endforelse
            </div>

            <!-- Desktop Table View -->
            <div
                class="hidden md:block bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-2xl overflow-hidden shadow-sm">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-50/50 dark:bg-gray-900/50 border-b border-gray-200 dark:border-gray-700">
                            <th
                                class="px-6 py-4 text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                Usuario</th>
                            <th
                                class="px-6 py-4 text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                Roles</th>
                            <th
                                class="px-6 py-4 text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                Estado</th>
                            <th
                                class="px-6 py-4 text-right text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                        @forelse($users as $user)
                            <tr
                                class="bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-slate-900/60 transition-colors group">
                                <td class="px-6 py-4 w-1/3">
                                    <div class="flex items-center gap-3">
                                        <img src="{{ $user->profile_photo_url }}" alt="{{ $user->name }}"
                                            class="h-10 w-10 rounded-full object-cover border border-gray-200 dark:border-gray-700 shadow-sm shrink-0">
                                        <div class="min-w-0">
                                            <div
                                                class="text-[14px] font-bold text-gray-900 dark:text-white group-hover:dark:text-white truncate">
                                                {{ $user->name }}
                                            </div>
                                            <div class="text-[13px] text-gray-500 dark:text-gray-400 mt-0.5 truncate">
                                                {{ $user->email }}
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex flex-wrap gap-1.5">
                                        @foreach($user->roles as $role)
                                            <span
                                                class="inline-flex items-center px-2 py-[3px] rounded text-[10px] font-black uppercase tracking-wider bg-slate-100 dark:bg-slate-700 text-slate-700 dark:text-slate-300">
                                                {{ $role->name }}
                                            </span>
                                        @endforeach
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    @if($user->trashed())
                                        <span
                                            class="inline-flex items-center px-2 py-1 rounded text-[10px] font-black uppercase tracking-wider text-red-600 dark:text-red-400 bg-red-50 dark:bg-red-900/20">Eliminado</span>
                                    @elseif(!$user->is_active)
                                        <span
                                            class="inline-flex items-center px-2 py-1 rounded text-[10px] font-black uppercase tracking-wider text-yellow-600 dark:text-yellow-400 bg-yellow-50 dark:bg-yellow-900/20">Inactivo</span>
                                    @else
                                        <span
                                            class="inline-flex items-center px-2 py-1 rounded text-[10px] font-black uppercase tracking-wider text-green-600 dark:text-green-400 bg-green-50 dark:bg-green-900/20">Activo</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <div class="flex justify-end gap-2">
                                        @if($user->trashed())
                                            <form action="{{ route('users.restore', $user->id) }}" method="POST"
                                                class="inline-block">
                                                @csrf
                                                <button type="submit"
                                                    class="inline-flex items-center px-3 py-1.5 bg-emerald-50 hover:bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:hover:bg-emerald-900/50 dark:text-emerald-400 text-xs font-bold rounded-lg transition-colors border border-emerald-100 dark:border-emerald-800/50">
                                                    Restaurar
                                                </button>
                                            </form>
                                        @else
                                            <a href="{{ route('users.edit', $user) }}" title="Editar usuario"
                                                class="inline-flex p-2 text-blue-500 hover:text-blue-700 hover:bg-blue-50 dark:text-blue-400 dark:hover:text-blue-300 dark:hover:bg-blue-500/20 rounded-lg transition-colors">
                                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                </svg>
                                            </a>
                                            <form id="delete-form-desktop-{{ $user->id }}"
                                                action="{{ route('users.destroy', $user) }}" method="POST" class="inline-block">
                                                @csrf
                                                @method('DELETE')
                                                <button type="button" title="Eliminar usuario"
                                                    @click="selectedId = {{ $user->id }}; $dispatch('open-modal', 'confirm-delete-desktop')"
                                                    class="inline-flex p-2 text-red-500 hover:text-red-700 hover:bg-red-50 dark:text-red-400 dark:hover:text-red-300 dark:hover:bg-red-500/20 rounded-lg transition-colors">
                                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                    </svg>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-12 text-center text-gray-500 dark:text-gray-400">
                                    No se encontraron usuarios registrados.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <x-modal-confirm name="confirm-delete-mobile" title="Eliminar Usuario"
                content="¿Estás seguro de eliminar este usuario? Esta acción no se puede deshacer.">
                <x-slot name="footer">
                    <x-secondary-button x-on:click="$dispatch('close')">
                        Cancelar
                    </x-secondary-button>

                    <x-danger-button class="ml-3"
                        x-on:click="document.getElementById('delete-form-mobile-' + selectedId).submit()">
                        Sí, eliminar
                    </x-danger-button>
                </x-slot>
            </x-modal-confirm>

            <x-modal-confirm name="confirm-delete-desktop" title="Eliminar Usuario"
                content="¿Estás seguro de eliminar este usuario? Esta acción no se puede deshacer.">
                <x-slot name="footer">
                    <x-secondary-button x-on:click="$dispatch('close')">
                        Cancelar
                    </x-secondary-button>

                    <x-danger-button class="ml-3"
                        x-on:click="document.getElementById('delete-form-desktop-' + selectedId).submit()">
                        Sí, eliminar
                    </x-danger-button>
                </x-slot>
            </x-modal-confirm>
        </div>
    </div>
</x-app-layout>