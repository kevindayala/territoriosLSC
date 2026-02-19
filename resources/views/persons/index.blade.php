<x-app-layout>
    <x-slot name="logo_url">{{ route('dashboard') }}</x-slot>

    <div class="pt-1 pb-12 md:py-12">
        {{-- Título de la página --}}
        <x-slot name="header">
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                    {{ __('Personas') }}
                </h2>
                <a href="{{ route('persons.create') }}"
                    class="md:hidden inline-flex items-center justify-center px-6 py-2 bg-blue-600 text-white rounded-full shadow-lg shadow-blue-500/20 active:scale-95 transition-all gap-1.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" />
                    </svg>
                    <span class="text-sm font-bold">Nueva</span>
                </a>
            </div>
        </x-slot>

        {{-- Barra de Filtros --}}
        <div class="mt-0 sm:mt-[20px] mb-8 py-2">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <form method="GET" action="{{ route('persons.index') }}"
                    class="flex flex-nowrap items-stretch gap-3 h-10">

                    <!-- City Filter -->
                    <div class="w-48 flex-shrink-0">
                        <select name="city_id" onchange="this.form.submit()"
                            class="w-full h-10 bg-white dark:bg-gray-700 border border-gray-200 dark:border-gray-600 text-gray-700 dark:text-gray-200 text-sm rounded-lg focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 px-3 m-0">
                            <option value="">Todas las Ciudades</option>
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
                            placeholder="Buscar por nombre, dirección o territorio...">
                    </div>

                    <!-- Add Button (Desktop Only) -->
                    <div class="hidden md:block flex-shrink-0">
                        <a href="{{ route('persons.create') }}"
                            class="flex items-center justify-center h-full px-5 bg-blue-600 hover:bg-blue-700 text-white border border-blue-600 rounded-lg text-sm font-bold shadow-sm transition-all whitespace-nowrap leading-none m-0 group">
                            <svg class="w-4 h-4 me-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 4v16m8-8H4" />
                            </svg>
                            <span class="hidden md:inline">Registrar Persona</span>
                        </a>
                    </div>
                </form>
            </div>
        </div>

        {{-- Contenido Principal --}}
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            <!-- Mobile Card View -->
            <div class="block md:hidden w-full space-y-4">
                @forelse($persons as $person)
                    <div
                        class="bg-white dark:bg-gray-800 p-5 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm">
                        <div class="flex justify-between items-start mb-4">
                            <div>
                                <h3 class="font-bold text-gray-900 dark:text-white">{{ $person->full_name }}</h3>
                                <p class="text-sm text-gray-600 dark:text-gray-300 mt-1">
                                    <span class="font-bold text-gray-700 dark:text-gray-200">Dirección:</span>
                                    {{ $person->address }}
                                </p>
                                <p class="text-xs text-gray-400 mt-1 font-medium">
                                    Territorio: {{ $person->territory->code ?? '-' }}
                                    @if($person->territory && $person->territory->neighborhood_name)
                                        - {{ $person->territory->neighborhood_name }}
                                    @endif
                                    <span class="mx-1">•</span>
                                    {{ $person->territory->city->name ?? '' }}
                                </p>
                                @if($person->notes)
                                    <p
                                        class="text-xs text-gray-600 dark:text-gray-300 mt-2 p-2 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                                        <span class="font-bold text-gray-700 dark:text-gray-200">Nota:</span>
                                        {{ $person->notes }}
                                    </p>
                                @endif
                            </div>
                        </div>



                        <div class="flex gap-4">
                            <a href="{{ route('persons.edit', $person) }}"
                                class="flex-1 flex items-center justify-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-bold rounded-xl transition-colors">
                                <svg class="w-4 h-4 me-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                </svg>
                                Editar
                            </a>

                            @if($person->map_url)
                                <a href="{{ $person->map_url }}" target="_blank"
                                    class="flex items-center justify-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-bold rounded-xl transition-colors">
                                    <svg class="w-4 h-4 me-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z">
                                        </path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    </svg>
                                    Ver Mapa
                                </a>
                            @endif
                        </div>
                    </div>
                @empty
                    <div
                        class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-sm text-center text-gray-500 dark:text-gray-400 border border-gray-100 dark:border-gray-700">
                        No se encontraron personas registradas.
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
                                Dirección</th>
                            <th
                                class="px-6 py-4 text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                Ubicación</th>
                            <th
                                class="px-6 py-4 text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                Notas</th>

                            <th
                                class="px-6 py-4 text-right text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                        @forelse($persons as $person)
                            <tr
                                class="bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-slate-900/60 transition-colors group">
                                <td class="px-6 py-4">
                                    <div
                                        class="text-sm font-bold text-gray-900 dark:text-white group-hover:dark:text-white">
                                        {{ $person->full_name }}
                                    </div>

                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm text-gray-600 dark:text-gray-300 group-hover:dark:text-white">
                                        {{ $person->address }}
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm text-gray-600 dark:text-gray-300">
                                        <span class="font-bold text-gray-800 dark:text-gray-200">
                                            {{ $person->territory->code ?? '-' }}
                                            @if($person->territory && $person->territory->neighborhood_name)
                                                - {{ $person->territory->neighborhood_name }}
                                            @endif
                                        </span>
                                        <span
                                            class="text-xs text-gray-400 block">{{ $person->territory->city->name ?? '' }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm text-gray-500 dark:text-gray-400 max-w-xs truncate"
                                        title="{{ $person->notes }}">
                                        {{ $person->notes ?? '-' }}
                                    </div>
                                </td>

                                <td class="px-6 py-4 text-right">
                                    <div class="flex justify-end gap-2">
                                        @if($person->map_url)
                                            <a href="{{ $person->map_url }}" target="_blank"
                                                class="inline-flex p-2 text-gray-500 hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-gray-700/50 rounded-lg transition-colors"
                                                title="Ver mapa">
                                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z">
                                                    </path>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                </svg>
                                            </a>
                                        @endif

                                        <a href="{{ route('persons.edit', $person) }}"
                                            class="inline-flex p-2 text-blue-600 hover:bg-blue-50 dark:text-blue-400 dark:hover:bg-blue-500/20 rounded-lg transition-colors"
                                            title="Editar">
                                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-12 text-center text-gray-500 dark:text-gray-400">
                                    No se encontraron personas registradas.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-6">
                {{ $persons->appends(request()->query())->links() }}
            </div>
        </div>
    </div>
</x-app-layout>