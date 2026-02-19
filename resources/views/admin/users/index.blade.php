<x-app-layout>
    {{-- El logo ahora reemplaza al botón de volver --}}
    <x-slot name="logo_url">{{ route('admin.settings') }}</x-slot>

    <div class="pt-6 pb-12 md:py-12" x-data="{ selectedId: null }">
        {{-- Título de la página --}}
        <x-slot name="header">
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                    {{ __('Usuarios') }}
                </h2>
                <a href="{{ route('users.create') }}"
                    class="md:hidden inline-flex items-center justify-center px-6 py-2 bg-blue-600 text-white rounded-full shadow-lg shadow-blue-500/20 active:scale-95 transition-all gap-1.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" />
                    </svg>
                    <span class="text-sm font-bold">Nuevo</span>
                </a>
            </div>
        </x-slot>

        {{-- Barra de Filtros --}}
        <div class="mt-[-1rem] sm:mt-[20px] mb-8 py-2">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <form method="GET" action="{{ route('users.index') }}"
                    class="flex flex-nowrap items-stretch gap-3 h-10">

                    <!-- Search Input -->
                    <div class="flex-1 min-w-0 relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </div>
                        <input type="text" name="search" value="{{ request('search') }}"
                            class="w-full h-full pl-10 bg-white dark:bg-gray-700 border border-gray-200 dark:border-gray-600 text-gray-700 dark:text-gray-200 text-sm rounded-lg focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 px-3 placeholder-gray-400 transition-all m-0"
                            placeholder="Buscar por nombre o email...">
                    </div>

                    <!-- Add Button (Desktop Only) -->
                    <div class="hidden md:block flex-shrink-0">
                        <a href="{{ route('users.create') }}"
                            class="flex items-center justify-center h-full px-5 bg-blue-600 hover:bg-blue-700 text-white border border-blue-600 rounded-lg text-sm font-bold shadow-sm transition-all whitespace-nowrap leading-none m-0 group">
                            <svg class="w-4 h-4 me-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 4v16m8-8H4" />
                            </svg>
                            <span class="hidden md:inline">Registrar Usuario</span>
                        </a>
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
                        class="bg-white dark:bg-gray-800 p-5 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm">
                        <div class="flex justify-between items-center mb-4">
                            <div>
                                <h3 class="font-bold text-gray-900 dark:text-white">{{ $user->name }}</h3>
                                <p class="text-sm text-gray-500 dark:text-gray-400">{{ $user->email }}</p>
                                <div class="mt-1 flex flex-wrap gap-1">
                                    @foreach($user->roles as $role)
                                        <span class="px-2 py-0.5 text-[10px] font-bold rounded-full bg-blue-100 text-blue-700">
                                            {{ ucfirst($role->name) }}
                                        </span>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                        <div class="flex gap-2">
                            <a href="{{ route('users.edit', $user) }}"
                                class="flex-1 flex items-center justify-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-bold rounded-xl transition-colors">
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
                                Nombre</th>
                            <th
                                class="px-6 py-4 text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                Email</th>
                            <th
                                class="px-6 py-4 text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                Roles</th>
                            <th
                                class="px-6 py-4 text-right text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                        @forelse($users as $user)
                            <tr
                                class="bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-slate-900/60 transition-colors group">
                                <td class="px-6 py-4">
                                    <div
                                        class="text-sm font-semibold text-gray-900 dark:text-white group-hover:dark:text-white">
                                        {{ $user->name }}
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm text-gray-600 dark:text-gray-300 group-hover:dark:text-white">
                                        {{ $user->email }}
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex flex-wrap gap-1">
                                        @foreach($user->roles as $role)
                                            <span
                                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300">
                                                {{ ucfirst($role->name) }}
                                            </span>
                                        @endforeach
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <div class="flex justify-end gap-2">
                                        <a href="{{ route('users.edit', $user) }}"
                                            class="inline-flex p-2 text-blue-600 hover:bg-blue-50 dark:text-blue-400 dark:hover:bg-blue-500/20 rounded-lg transition-colors">
                                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                        </a>
                                        <form id="delete-form-desktop-{{ $user->id }}"
                                            action="{{ route('users.destroy', $user) }}" method="POST" class="inline-block">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button"
                                                @click="selectedId = {{ $user->id }}; $dispatch('open-modal', 'confirm-delete-desktop')"
                                                class="inline-flex p-2 text-red-600 hover:bg-red-50 dark:text-red-400 dark:hover:bg-red-500/20 rounded-lg transition-colors">
                                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                            </button>
                                        </form>
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