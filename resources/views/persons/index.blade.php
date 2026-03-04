<x-app-layout>
    <x-slot name="title">Personas</x-slot>
    <x-slot name="logo_url">{{ route('dashboard') }}</x-slot>

    <div class="pt-1 pb-12 md:py-12" x-data="{ showFilters: false, selectedId: null }">
        {{-- Título de la página --}}
        <x-slot name="header">
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                    {{ __('Personas') }}
                </h2>
                <a href="{{ route('persons.create', ['redirect_to' => request()->fullUrl()]) }}"
                    class="inline-flex items-center justify-center px-4 md:px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-full shadow-lg shadow-blue-500/20 active:scale-95 transition-all gap-1.5 focus:outline-none">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" />
                    </svg>
                    <span class="text-sm font-bold hidden md:inline">Registrar Persona</span>
                    <span class="text-sm font-bold md:hidden">Nueva</span>
                </a>
            </div>
        </x-slot>

        {{-- Top Navigation & Search --}}
        <div class="mt-4 sm:mt-6 mb-8">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <form method="GET" action="{{ route('persons.index') }}" class="w-full">
                    <div
                        class="border-b border-gray-200 dark:border-gray-700 pb-3 mb-2 flex flex-col sm:flex-row items-center justify-between gap-4">
                        <div class="flex items-center w-full">
                            <input type="text" name="search" value="{{ request('search') }}"
                                class="flex-1 border border-gray-400 dark:border-gray-600 rounded bg-white dark:bg-gray-800 focus:ring-1 focus:ring-gray-500 focus:border-gray-500 text-[15px] placeholder-gray-500 dark:placeholder-gray-400 text-gray-800 dark:text-gray-200 px-3 py-1.5 outline-none shadow-sm"
                                placeholder="{{ __('Buscar por nombre, dirección o territorio...') }}">
                            <div class="flex items-center gap-3 pl-3">
                                <button type="button" @click="showFilters = !showFilters"
                                    class="text-black dark:text-gray-200 hover:text-gray-700 dark:hover:text-white transition-colors focus:outline-none flex-shrink-0">
                                    <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                        stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                        <line x1="4" y1="21" x2="4" y2="14" />
                                        <line x1="4" y1="10" x2="4" y2="3" />
                                        <line x1="12" y1="21" x2="12" y2="12" />
                                        <line x1="12" y1="8" x2="12" y2="3" />
                                        <line x1="20" y1="21" x2="20" y2="16" />
                                        <line x1="20" y1="12" x2="20" y2="3" />
                                        <line x1="1" y1="14" x2="7" y2="14" />
                                        <line x1="9" y1="8" x2="15" y2="8" />
                                        <line x1="17" y1="16" x2="23" y2="16" />
                                    </svg>
                                </button>
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
                    </div>

                    {{-- Collapsible Filters --}}
                    <div x-show="showFilters" x-transition:enter="transition ease-out duration-200"
                        x-transition:enter-start="opacity-0 -translate-y-2"
                        x-transition:enter-end="opacity-100 translate-y-0"
                        x-transition:leave="transition ease-in duration-150"
                        x-transition:leave-start="opacity-100 translate-y-0"
                        x-transition:leave-end="opacity-0 -translate-y-2"
                        class="mt-4 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl p-6 shadow-sm mb-4"
                        style="display: none;">
                        <div class="flex flex-col gap-6">
                            @php $activeCityId = request('city_id'); @endphp
                            <div class="w-full">
                                <label
                                    class="block text-[13px] font-bold text-[#334155] dark:text-gray-400 tracking-wide mb-2">{{ __('Ciudad') }}</label>
                                <select name="city_id" onchange="this.form.submit()"
                                    class="block w-full sm:w-[340px] rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-[#0f172a] dark:text-gray-200 text-[15px] font-medium focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 py-2.5 pl-3.5 pr-10 shadow-sm transition-colors cursor-pointer">
                                    <option value="" @if($activeCityId === '' || !$activeCityId) selected @endif>
                                        {{ __('Todas las ciudades') }}
                                    </option>
                                    @foreach ($cities as $city)
                                        <option value="{{ $city->id }}" @if($activeCityId == $city->id) selected @endif>
                                            {{ $city->display_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        {{-- Contenido Principal --}}
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            {{-- Mobile Cards View --}}
            <div class="block md:hidden space-y-6">
                @forelse($persons as $person)
                    <div
                        class="bg-white dark:bg-gray-800 rounded-[2rem] border border-gray-200 dark:border-gray-700 overflow-hidden shadow-sm">
                        <div class="p-6 sm:p-7 border-b border-gray-100 dark:border-gray-800">
                            <div
                                class="font-black text-[1.15rem] text-gray-900 dark:text-white leading-tight mb-0.5 tracking-tight">
                                {{ $person->full_name }}
                            </div>
                            <div class="flex flex-col mb-2.5">
                                <span
                                    class="font-bold text-gray-800 dark:text-gray-200 text-[10px] uppercase tracking-widest mb-0.5 text-left">
                                    {{ $person->territory->neighborhood_name ?? '-' }}
                                </span>
                                <div class="flex items-center justify-start gap-1.5 flex-wrap">
                                    <span class="font-black text-blue-600 dark:text-blue-400 text-[13px] shrink-0">
                                        {{ $person->territory->code ?? '-' }}
                                    </span>
                                    @if($person->territory && $person->territory->city)
                                        <span class="text-gray-300 dark:text-gray-600 text-[10px]">•</span>
                                        <span class="text-[12px] font-medium text-gray-500 dark:text-gray-400">
                                            {{ $person->territory->city->name }}
                                        </span>
                                    @endif
                                </div>
                            </div>
                            <div class="text-[13px] text-gray-500 font-medium leading-relaxed mb-1">{{ $person->address }}
                            </div>
                            @if($person->notes)
                                <div class="mt-2 text-[11px] text-gray-500 dark:text-gray-400 leading-relaxed">
                                    <span class="font-bold">Nota:</span> {{ $person->notes }}
                                </div>
                            @endif
                        </div>

                        <div class="p-4 sm:p-5 flex justify-end items-center bg-gray-50/50 dark:bg-black/10">

                            <div class="flex gap-3">
                                @if($person->map_url)
                                    <a href="{{ $person->map_url }}" target="_blank"
                                        class="bg-green-50 text-green-600 dark:bg-green-900/20 dark:text-green-400 p-2.5 rounded-xl border border-green-100 dark:border-green-800/50 transition-colors"
                                        title="Ver Mapa">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z">
                                            </path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        </svg>
                                    </a>
                                @endif
                                <a href="{{ route('persons.edit', ['person' => $person, 'redirect_to' => request()->fullUrl()]) }}"
                                    class="bg-blue-50 hover:bg-blue-100 text-blue-600 dark:bg-blue-900/20 dark:hover:bg-blue-900/40 dark:text-blue-400 px-4 py-2.5 rounded-xl text-xs font-black transition-colors flex items-center justify-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                    EDITAR
                                </a>
                            </div>
                        </div>
                    </div>
                @empty
                    <div
                        class="bg-white dark:bg-gray-800 p-8 rounded-[2rem] shadow-sm text-center text-gray-500 dark:text-gray-400 border border-gray-100 dark:border-gray-700">
                        No se encontraron personas registradas.
                    </div>
                @endforelse
            </div>

            {{-- Desktop Table View --}}
            <div
                class="hidden md:block bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-2xl overflow-hidden shadow-sm">
                <table class="w-full min-w-full divide-y divide-gray-100 dark:divide-gray-700">
                    <thead class="bg-gray-50/50 dark:bg-gray-900/50 text-gray-500">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider">Nombre /
                                Dirección</th>
                            <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider">Territorio /
                                Ciudad</th>
                            <th class="px-6 py-4 text-right text-xs font-bold uppercase tracking-wider">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50 dark:divide-gray-800">
                        @forelse($persons as $person)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition">
                                <td class="px-6 py-4">
                                    <div class="font-bold text-gray-900 dark:text-white leading-tight mb-1">
                                        {{ $person->full_name }}
                                    </div>
                                    <div class="text-[11px] text-gray-500 max-w-xs break-words leading-relaxed">
                                        {{ $person->address }}
                                    </div>
                                    @if($person->notes)
                                        <div class="mt-2 text-[10px] text-gray-400 bg-gray-50 dark:bg-gray-800 px-2 py-1 rounded inline-block max-w-xs overflow-hidden text-ellipsis whitespace-nowrap"
                                            title="{{ $person->notes }}">
                                            📝 {{ $person->notes }}
                                        </div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div
                                        class="font-bold text-gray-900 dark:text-white text-[13px] uppercase tracking-wide mb-1 truncate max-w-[180px]">
                                        {{ $person->territory->neighborhood_name ?? '-' }}
                                    </div>
                                    <div class="flex items-center gap-1.5">
                                        <span class="font-black text-blue-600 dark:text-blue-400 text-[11px]">
                                            {{ $person->territory->code ?? '-' }}
                                        </span>
                                        @if($person->territory && $person->territory->city)
                                            <span class="text-gray-300 dark:text-gray-600 text-[10px]">•</span>
                                            <span
                                                class="text-[11px] font-medium text-gray-500 dark:text-gray-400 truncate max-w-[120px]">
                                                {{ $person->territory->city->name }}
                                            </span>
                                        @endif
                                    </div>
                                </td>

                                <td class="px-6 py-4 whitespace-nowrap text-right">
                                    <div class="flex items-center justify-end gap-2.5">
                                        @if($person->map_url)
                                            <a href="{{ $person->map_url }}" target="_blank"
                                                class="p-2.5 text-gray-400 hover:text-green-600 hover:bg-green-50 dark:hover:bg-green-500/10 rounded-2xl transition-all"
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

                                        <a href="{{ route('persons.edit', ['person' => $person, 'redirect_to' => request()->fullUrl()]) }}"
                                            class="flex items-center gap-1.5 p-2 px-3 text-blue-600 bg-blue-50 hover:bg-blue-100 dark:text-blue-400 dark:bg-blue-900/20 dark:hover:bg-blue-900/40 rounded-2xl transition-all font-black text-[10px]"
                                            title="Editar persona">
                                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                            EDITAR
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="px-6 py-12 text-center text-gray-500 dark:text-gray-400">
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

            <x-modal-confirm name="confirm-delete" title="Eliminar Persona"
                content="¿Estás seguro de eliminar este registro de persona? Esta acción no se puede deshacer.">
                <x-slot name="footer">
                    <x-secondary-button x-on:click="$dispatch('close')">Cancelar</x-secondary-button>
                    <x-danger-button class="ml-3"
                        x-on:click="document.getElementById('delete-desktop-' + selectedId).submit()">Sí,
                        eliminar</x-danger-button>
                </x-slot>
            </x-modal-confirm>
        </div>
    </div>
</x-app-layout>