<x-app-layout>
    <x-slot name="title">Lista de Territorios</x-slot>
    {{-- El logo ahora reemplaza al botón de volver --}}
    <x-slot name="logo_url">{{ route('dashboard') }}</x-slot>

    <div class="pt-2 pb-12 md:py-12" x-data="{ showFilters: false }" @open-filters.window="showFilters = true">
        {{-- Título de la página --}}
        <x-slot name="header">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Territorios') }}
            </h2>
        </x-slot>

        {{-- Top Navigation & Search --}}
        <div class="mt-4 sm:mt-6 mb-8">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                
                {{-- Filters & Search Row --}}
                <div class="mb-8">
                    <form method="GET" action="{{ route('territories.index') }}" class="w-full">
                        <input type="hidden" name="form_submitted" value="1">
                        {{-- Clean Search Bar --}}
                        <div class="border-b border-gray-200 dark:border-gray-700 pb-3 mb-2">
                            <div class="flex items-center w-full">
                                <input type="text" name="search" id="search" value="{{ request('search') }}"
                                    placeholder="{{ __('Buscar') }}"
                                    class="flex-1 border border-gray-400 dark:border-gray-600 rounded bg-white dark:bg-gray-800 focus:ring-1 focus:ring-gray-500 focus:border-gray-500 text-[15px] placeholder-gray-500 dark:placeholder-gray-400 text-gray-800 dark:text-gray-200 px-3 py-1.5 outline-none shadow-sm">
                                
                                <div class="flex items-center gap-3 pl-3">
                                    <button type="button" @click="showFilters = !showFilters" class="text-black dark:text-gray-200 hover:text-gray-700 dark:hover:text-white transition-colors focus:outline-none flex-shrink-0">
                                        <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
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
                                    
                                    <button type="submit" class="text-black dark:text-gray-200 hover:text-gray-700 dark:hover:text-white transition-colors focus:outline-none flex-shrink-0">
                                        <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                            <circle cx="11" cy="11" r="8"></circle>
                                            <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>

                        {{-- Collapsible Filters --}}
                        <div x-show="showFilters" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 -translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 -translate-y-2" class="mt-4 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl p-6 shadow-sm" style="display: none;">
                            <div class="flex flex-col gap-6">
                                {{-- City Selection --}}
                                @php $activeCityId = request('city_id'); @endphp
                                <div class="w-full">
                                    <label class="block text-[13px] font-bold text-[#334155] dark:text-gray-400 tracking-wide mb-2">{{ __('Ciudad') }}</label>
                                    <select name="city_id" onchange="this.form.submit()" class="block w-full sm:w-[340px] rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-[#0f172a] dark:text-gray-200 text-[15px] font-medium focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 py-2.5 px-3.5 shadow-sm transition-colors cursor-pointer">
                                        <option value="todas" @if($activeCityId === 'todas' || !$activeCityId) selected @endif>{{ __('Todas las ciudades') }}</option>
                                        @foreach ($cities as $city)
                                            <option value="{{ $city->id }}" @if($activeCityId == $city->id) selected @endif>
                                                {{ $city->display_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                {{-- Status Filter --}}
                                <div>
                                    <label class="block text-[13px] font-bold text-[#334155] dark:text-gray-400 tracking-wide mb-3">{{ __('Filtrar por') }}</label>
                                    <div class="flex flex-col gap-3.5">

                                        <label class="group flex items-center gap-3 cursor-pointer w-max">
                                            <input type="checkbox" name="filter[]" value="recommended" onchange="this.form.submit()" 
                                                {{ in_array('recommended', (array) request('filter')) ? 'checked' : '' }}
                                                class="w-[18px] h-[18px] rounded border-gray-300 dark:border-gray-600 text-blue-600 focus:ring-blue-500/30 dark:bg-gray-800 transition-colors cursor-pointer bg-white shadow-sm">
                                            <span class="text-[15px] text-[#0f172a] dark:text-gray-200 select-none">
                                                {{ __('Territorios sugeridos') }}
                                            </span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>

            </div>

            @if(isset($topAssignedTerritories) && $topAssignedTerritories->isNotEmpty())
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mb-12">
                <div class="flex items-center gap-4 mb-6">
                    <h2 class="text-2xl md:text-3xl font-extrabold bg-clip-text text-transparent bg-gradient-to-r from-blue-700 to-blue-500 dark:from-blue-400 dark:to-blue-300 drop-shadow-sm tracking-tight">{{ __('Territorios Asignados') }}</h2>
                    <div class="flex-1 h-px bg-gradient-to-r from-blue-200 to-transparent dark:from-blue-900/50"></div>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 items-stretch">
                    @foreach($topAssignedTerritories as $territory)
                        @include('territories.partials._card', ['territory' => $territory])
                    @endforeach
                </div>
            </div>
            @endif

            <!-- Territories List -->
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                @php
                    $currentCity = null;
                    $currentPriority = null;
                @endphp
                <!-- Responsive Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 items-stretch">
                    @forelse ($territories as $territory)


                        @if($currentCity !== $territory->city_id)
                            <div class="col-span-full mt-12 mb-6">
                                <div class="flex items-center gap-4">
                                    <h2 class="text-2xl md:text-3xl font-extrabold bg-clip-text text-transparent bg-gradient-to-r from-blue-700 to-blue-500 dark:from-blue-400 dark:to-blue-300 drop-shadow-sm tracking-tight">{{ $territory->city->name }}</h2>
                                    <div class="flex-1 h-px bg-gradient-to-r from-blue-200 to-transparent dark:from-blue-900/50"></div>
                                </div>
                            </div>
                        @endif
                        @php $currentCity = $territory->city_id @endphp

                        @include('territories.partials._card', ['territory' => $territory])
                    @empty
                        @php 
                                                                            $filtersArr = (array) request('filter');
                            $hasGlobalFilters = request('search') || !empty(array_intersect($filtersArr, ['my_assignments', 'assigned_today', 'recommended'])); 
                        @endphp
                        @if((!$activeCityId || $activeCityId === 'todas') && !$hasGlobalFilters && !request()->has('form_submitted') && (!isset($topAssignedTerritories) || $topAssignedTerritories->isEmpty()))
                            <div class="col-span-full py-16 px-6 mt-8 bg-white dark:bg-gray-800 shadow-sm rounded-none sm:rounded-xl text-center animate-fade-in max-w-xl mx-auto w-full">
                                <div class="mx-auto h-[72px] w-[72px] bg-[#f0f4ff] dark:bg-blue-900/40 rounded-full flex items-center justify-center mb-6">
                                    <svg class="h-8 w-8 text-[#3b82f6] dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path>
                                        <circle cx="12" cy="10" r="3"></circle>
                                    </svg>
                                </div>
                                <h3 class="text-[22px] font-semibold text-[#0f172a] dark:text-white mb-3 tracking-tight">{{ __('Selecciona una ciudad') }}</h3>
                                <p class="text-[#64748b] dark:text-gray-400 text-[15px] leading-relaxed mx-2">
                                    {{ __('Para empezar, elige una ciudad arriba para ver los territorios o usa el buscador para encontrar un territorio específico.') }}
                                </p>
                            </div>
                        @elseif(!((!$activeCityId || $activeCityId === 'todas') && !$hasGlobalFilters && !request()->has('form_submitted')))
                            <div class="col-span-1 md:col-span-2 lg:col-span-3 py-12 text-center bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 border-dashed">
                                 <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-gray-50 dark:bg-gray-700 mb-4">
                                    <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                                 </div>
                                 <h3 class="text-lg font-medium text-gray-900 dark:text-white">{{ __('No se encontraron resultados') }}</h3>
                                 <p class="text-gray-500 dark:text-gray-400 max-w-sm mx-auto mt-2">{{ __('Intenta buscar con otros términos o limpia los filtros para ver todos los territorios.') }}</p>
                            </div>
                        @endif
                    @endforelse
                </div>

                @if(method_exists($territories, 'hasPages') && $territories->hasPages())
                    <div class="mt-8">
                        {{ $territories->links() }}
                    </div>
                @endif
            </div>
        </div>

        </div>
</x-app-layout>