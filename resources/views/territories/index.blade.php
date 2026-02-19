<x-app-layout>
    {{-- El logo ahora reemplaza al botón de volver --}}
    <x-slot name="logo_url">{{ route('dashboard') }}</x-slot>

    <div class="pt-2 pb-12 md:py-12" x-data="{ showFilters: false }" @open-filters.window="showFilters = true">
        {{-- Título de la página --}}
        <x-slot name="header">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Territorios') }}
            </h2>
        </x-slot>

        {{-- Barra de Filtros (Fondo Completo) --}}
        <div class="mt-0 sm:mt-[20px] mb-6 py-2">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <!-- Filters Container -->
                <div class="mb-6">
                    <form method="GET" action="{{ route('territories.index') }}"
                        class="flex flex-row gap-2 items-center" id="filterForm">
                        @if(request('sort_by')) <input type="hidden" name="sort_by" value="{{ request('sort_by') }}">
                        @endif
                        @if(request('sort_order')) <input type="hidden" name="sort_order"
                        value="{{ request('sort_order') }}"> @endif

                        <!-- City Filter -->
                        <div class="w-1/3 md:w-64">
                            <select name="city_id" id="city_id" onchange="this.form.submit()"
                                class="block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm h-10 text-sm">
                                <option value="">{{ __('Todas las ciudades') }}</option>
                                @foreach ($cities as $city)
                                    <option value="{{ $city->id }}" {{ request('city_id') == $city->id ? 'selected' : '' }}>
                                        {{ $city->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Search Input -->
                        <div class="relative flex-1 md:flex-none md:w-96">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg"
                                    viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd"
                                        d="M9 3.5a5.5 5.5 0 100 11 5.5 5.5 0 000-11zM2 9a7 7 0 1112.452 4.391l3.328 3.329a.75.75 0 11-1.06 1.06l-3.329-3.328A7 7 0 012 9z"
                                        clip-rule="evenodd" />
                                </svg>
                            </div>
                            <input type="text" name="search" id="search" value="{{ request('search') }}"
                                placeholder="{{ __('Buscar territorio o código') }}"
                                class="block w-full pl-10 border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm h-10 text-sm">
                        </div>

                        <!-- Hidden Submit Button for Enter Key -->
                        <button type="submit" class="hidden"></button>
                    </form>
                </div>
            </div>

            <!-- Territories List -->
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <!-- Responsive Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 items-stretch">
                    @forelse ($territories as $territory)
                        @php
                            $isAssigned = $territory->assignments->whereNull('completed_at')->isNotEmpty();
                            $monthsDiff = $territory->last_completed_at ? $territory->last_completed_at->diffInMonths(now()) : null;
                            $priorityClass = 'border-l-4 border-t border-r border-b border-gray-200 dark:border-gray-700';
                            
                            $borderColor = '#d1d5db'; // default gray
                            $warningType = 'none';
                            $warningText = '';

                            if ($isAssigned) {
                                $borderColor = '#d1d5db'; // gray-300
                                $warningType = 'gray';
                                $warningText = __('Este territorio esta asignado.');
                            } elseif (!$territory->last_completed_at || $monthsDiff >= 6) {
                                $borderColor = '#22c55e'; // green-500
                                $warningType = 'green';
                                $warningText = __('Se recomienda hacer este territorio.');
                            } elseif ($monthsDiff >= 2) {
                                $borderColor = '#eab308'; // yellow-500
                                $warningType = 'yellow';
                                $warningText = __('Este territorio se hizo hace algunos meses.');
                            } else {
                                $borderColor = '#ef4444'; // red-500
                                $warningType = 'red';
                                $warningText = __('Este territorio se realizó recientemente.');
                            }
                        @endphp
                        <div class="flex flex-col h-full">
                            <a href="{{ route('territories.show', $territory) }}" class="group flex flex-col flex-1 bg-white dark:bg-gray-800 rounded-2xl p-6 shadow-sm hover:shadow-lg hover:-translate-y-0.5 transition-all duration-300 relative active:scale-[0.99] {{ $priorityClass }}">
                            
                            <!-- 1) Header row -->
                            <div class="flex justify-between items-start mb-2">
                                <h3 class="text-xl font-bold text-gray-900 dark:text-white leading-tight">
                                    {{ $territory->neighborhood_name }}
                                </h3>
                                
                                @if ($isAssigned)
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300 border border-red-200 dark:border-red-800">
                                        {{ __('Asignado') }}
                                    </span>
                                @endif
                            </div>

                            <!-- 2) Subtitle row -->
                            <div class="text-sm text-gray-500 dark:text-gray-400 mb-6">
                                {{ $territory->code }} &middot; {{ $territory->city->name }}
                            </div>

                            <!-- 3) Metrics row -->
                            <div class="flex flex-col gap-2 text-[13px] text-gray-600 dark:text-gray-300 mb-6">
                                <!-- Persons -->
                                <div class="flex items-center gap-3">
                                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                                    @if($territory->persons_count === 0)
                                        <span class="text-gray-400 italic">{{ __('Sin personas registradas') }}</span>
                                    @else
                                        <span><span class="font-bold">{{ __('Personas:') }}</span> {{ $territory->persons_count }}</span>
                                    @endif
                                </div>

                                <!-- Last Done -->
                                @php
                                    $lastDateText = __('Nunca realizado');
                                    if($territory->last_completed_at) {
                                         if ($territory->last_completed_at->isToday()) {
                                              $lastDateText = __('Hoy');
                                          } elseif ($territory->last_completed_at->isYesterday()) {
                                              $lastDateText = __('Ayer');
                                          } else {
                                              $lastDateText = ucfirst($territory->last_completed_at->diffForHumans());
                                          }
                                    }
                                @endphp
                                <div class="flex items-center gap-3">
                                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    <span>
                                        <span class="font-bold">{{ __('Última vez realizado:') }}</span> 
                                        @if(!$territory->last_completed_at)
                                            <span class="font-bold text-gray-900 dark:text-gray-100">{{ $lastDateText }}</span>
                                        @else
                                            {{ $lastDateText }}
                                        @endif
                                    </span>
                                </div>

                                <!-- Completions this year -->
                                <div class="flex items-center gap-3">
                                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                                    @php
                                        $annualCount = $territory->annual_completions_count ?? 0;
                                    @endphp
                                    <span><span class="font-bold">{{ __('Realizado este año:') }}</span> {{ $annualCount }} {{ $annualCount == 1 ? __('vez') : __('veces') }}</span>
                                </div>
                            </div>

                            <!-- Priority Warning -->
                            @hasanyrole('admin|capitan')
                                @if($warningType !== 'none')
                                    <div class="mb-4 flex items-start gap-3 p-4 rounded-r-xl border-l-[6px] text-xs font-medium {{ $warningType === 'red' ? 'bg-red-100 border-red-500 text-gray-800 dark:bg-red-900/40 dark:text-gray-200' : ($warningType === 'yellow' ? 'bg-yellow-100 border-yellow-500 text-gray-800 dark:bg-yellow-900/20 dark:text-gray-300' : ($warningType === 'gray' ? 'bg-gray-100 border-gray-500 text-gray-800 dark:bg-gray-700 dark:text-gray-300' : 'bg-green-100 border-green-500 text-gray-800 dark:bg-green-900/20 dark:text-gray-300')) }}">
                                        <svg class="w-5 h-5 shrink-0 {{ $warningType === 'red' ? 'text-red-500' : ($warningType === 'yellow' ? 'text-yellow-600' : ($warningType === 'gray' ? 'text-gray-500' : 'text-green-600')) }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                                        </svg>
                                        <span class="mt-0.5">{!! $warningText !!}</span>
                                    </div>
                                @endif
                            @endhasanyrole

                            <!-- 4) Primary action -->
                            <div class="mt-auto pt-2">
                                <div class="flex items-center justify-center w-full h-12 bg-blue-600 hover:bg-blue-700 text-white text-base font-bold rounded-xl shadow-md transition-all active:scale-[0.98]">
                                    {{ __('Ver territorio') }}
                                </div>
                            </div>
                            </a>
                        </div>
                    @empty
                        <div class="col-span-1 md:col-span-2 lg:col-span-3 py-12 text-center bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 border-dashed">
                             <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-gray-50 dark:bg-gray-700 mb-4">
                                <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                             </div>
                             <h3 class="text-lg font-medium text-gray-900 dark:text-white">{{ __('No se encontraron resultados') }}</h3>
                             <p class="text-gray-500 dark:text-gray-400 max-w-sm mx-auto mt-2">{{ __('Intenta buscar con otros términos o limpia los filtros para ver todos los territorios.') }}</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
</x-app-layout>