<x-app-layout>
    {{-- El logo ahora reemplaza al botón de volver --}}
    <x-slot name="logo_url">{{ route('admin.settings') }}</x-slot>

    <div class="pt-6 pb-12 md:py-12">
        {{-- Título de la página --}}
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mb-8 md:mb-12 flex items-center justify-between">
            <h2 class="font-bold text-3xl text-gray-900 dark:text-white">
                {{ __('Barrios') }}
            </h2>
            <a href="{{ route('neighborhoods.create') }}"
                class="md:hidden inline-flex items-center justify-center px-6 py-2 bg-blue-600 text-white rounded-full shadow-lg shadow-blue-500/20 active:scale-95 transition-all gap-1.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" />
                </svg>
                <span class="text-sm font-bold">Nuevo</span>
            </a>
        </div>

        {{-- Barra de Filtros (Fondo Completo) --}}
        <div class="mt-[-1rem] sm:mt-[20px] mb-8 py-2">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <form method="GET" action="{{ route('neighborhoods.index') }}"
                    class="flex flex-nowrap items-stretch gap-3 h-10">
                    <!-- City Selector -->
                    <div class="w-32 flex-shrink-0">
                        <select name="city_id" onchange="this.form.submit()"
                            class="w-full h-full bg-white dark:bg-gray-700 border border-gray-200 dark:border-gray-600 text-gray-700 dark:text-gray-200 text-sm rounded-lg focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 px-3 transition-all cursor-pointer m-0">
                            <option value="">Ciudad</option>
                            @foreach ($cities as $city)
                                <option value="{{ $city->id }}" {{ request('city_id') == $city->id ? 'selected' : '' }}>
                                    {{ $city->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

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
                            placeholder="Buscar por nombre de barrio...">
                    </div>

                    <!-- Add Button (Desktop Only) -->
                    <div class="hidden md:block flex-shrink-0">
                        <a href="{{ route('neighborhoods.create') }}"
                            class="flex items-center justify-center h-full px-5 bg-blue-600 hover:bg-blue-700 text-white border border-blue-600 rounded-lg text-sm font-bold shadow-sm transition-all whitespace-nowrap leading-none m-0 group">
                            <svg class="w-4 h-4 me-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 4v16m8-8H4" />
                            </svg>
                            <span class="hidden md:inline">Registrar Barrio</span>
                        </a>
                    </div>
                </form>
            </div>
        </div>

        {{-- Contenido Principal (Grilla/Tabla) --}}
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Mobile Card View (Visible ONLY on small screens < md) -->
            <div class="block md:hidden w-full space-y-4">
                @forelse($neighborhoods as $neighborhood)
                    <div
                        class="bg-white dark:bg-gray-800 p-5 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm">
                        <div class="flex justify-between items-center mb-4">
                            <div>
                                <h3 class="font-bold text-gray-900 dark:text-white">{{ $neighborhood->name }}</h3>
                                <p class="text-sm text-gray-500 dark:text-gray-400">{{ $neighborhood->city->name }}</p>
                            </div>
                            <div class="flex items-center">
                                <div
                                    class="h-1.5 w-1.5 rounded-full me-1.5 {{ $neighborhood->is_active ? 'bg-green-500 shadow-[0_0_8px_rgba(34,197,94,0.4)]' : 'bg-red-500 shadow-[0_0_8_rgba(239,68,68,0.4)]' }}">
                                </div>
                                <span
                                    class="text-[10px] font-black uppercase tracking-wider {{ $neighborhood->is_active ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
                                    {{ $neighborhood->is_active ? 'Activo' : 'Inactivo' }}
                                </span>
                            </div>
                        </div>
                        <a href="{{ route('neighborhoods.edit', $neighborhood) }}"
                            class="flex items-center justify-center w-full px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-bold rounded-xl transition-colors">
                            <svg class="w-4 h-4 me-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                            </svg>
                            Editar Barrio
                        </a>
                    </div>
                @empty
                    <div
                        class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-sm text-center text-gray-500 dark:text-gray-400 border border-gray-100 dark:border-gray-700">
                        No hay barrios registrados que coincidan con la búsqueda.
                    </div>
                @endforelse
            </div>

            <!-- Desktop Table View (Visible ONLY on screens >= md) -->
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
                                Ciudad</th>
                            <th
                                class="px-6 py-4 text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                Estado</th>
                            <th
                                class="px-6 py-4 text-right text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                        @forelse($neighborhoods as $neighborhood)
                            <tr
                                class="bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-slate-900/60 transition-colors group">
                                <td class="px-6 py-4">
                                    <div
                                        class="text-sm font-semibold text-gray-900 dark:text-white group-hover:dark:text-white">
                                        {{ $neighborhood->name }}
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm text-gray-600 dark:text-gray-300 group-hover:dark:text-white">
                                        {{ $neighborhood->city->name }}
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center">
                                        <div
                                            class="h-2 w-2 rounded-full me-2 {{ $neighborhood->is_active ? 'bg-green-500 shadow-[0_0_8px_rgba(34,197,94,0.4)]' : 'bg-red-500 shadow-[0_0_8px_rgba(239,68,68,0.4)]' }}">
                                        </div>
                                        <span
                                            class="text-sm font-medium {{ $neighborhood->is_active ? 'text-green-700 dark:text-green-400' : 'text-red-700 dark:text-red-400' }}">
                                            {{ $neighborhood->is_active ? 'Activo' : 'Inactivo' }}
                                        </span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <a href="{{ route('neighborhoods.edit', $neighborhood) }}"
                                        class="inline-flex p-2 text-blue-600 hover:bg-blue-50 dark:text-blue-400 dark:hover:bg-blue-500/20 rounded-lg transition-colors">
                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-12 text-center text-gray-500 dark:text-gray-400">
                                    No se encontraron barrios registrados.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>