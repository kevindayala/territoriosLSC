<x-app-layout>
    <x-slot name="logo_url">{{ route('persons.index') }}</x-slot>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Registrar Persona') }}
        </h2>
    </x-slot>

    <div class="py-6 px-4 max-w-7xl mx-auto">
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
            <form method="POST" action="{{ route('persons.store') }}">
                @csrf
                <input type="hidden" name="redirect_to" value="{{ request('redirect_to', route('persons.index')) }}">

                {{-- Full Name --}}
                <div class="mb-5">
                    <label
                        class="flex items-center gap-2 text-[13px] font-bold text-gray-700 dark:text-gray-300 mb-2 uppercase tracking-wide">
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                        Nombre Completo
                    </label>
                    <input type="text" name="full_name" value="{{ old('full_name') }}" required
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-900 dark:border-gray-700 dark:text-gray-300">
                    <x-input-error :messages="$errors->get('full_name')" class="mt-2" />
                </div>

                {{-- Address --}}
                <div class="mb-5">
                    <label
                        class="flex items-center gap-2 text-[13px] font-bold text-gray-700 dark:text-gray-300 mb-2 uppercase tracking-wide">
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z">
                            </path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                        Dirección
                    </label>
                    <textarea name="address" rows="3" required
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-900 dark:border-gray-700 dark:text-gray-300">{{ old('address') }}</textarea>
                    <x-input-error :messages="$errors->get('address')" class="mt-2" />
                </div>

                {{-- Territory Searchable Select --}}
                @php
                    $territoriesData = $territories->map(function ($t) {
                        return [
                            'id' => $t->id,
                            'code' => $t->code,
                            'city_name' => $t->city ? $t->city->name : '',
                            'name' => $t->neighborhood_name ?? 'Sin barrio',
                            'type' => $t->type,
                            'display' => $t->code . ($t->city ? ' - ' . $t->city->name : '') . ' - ' . ($t->neighborhood_name ?? 'Sin barrio') . ($t->type === 'business' ? ' (Negocios)' : '')
                        ];
                    })->values()->toJson();

                    $oldTerritoryId = old('territory_id', $initialTerritory ? $initialTerritory->id : null);
                @endphp

                <div class="mb-5" x-data="{
                        open: false,
                        search: '',
                        territories: {{ $territoriesData }},
                        selectedId: {{ $oldTerritoryId ? $oldTerritoryId : 'null' }},
                        get filteredTerritories() {
                            if (this.search === '') return []; 
                            let searchClean = this.search.toLowerCase().replace(/\s/g, '');
                            return this.territories.filter(t => 
                                t.code.toLowerCase().replace(/\s/g, '').includes(searchClean) || 
                                t.name.toLowerCase().includes(this.search.toLowerCase()) ||
                                t.city_name.toLowerCase().includes(this.search.toLowerCase())
                            );
                        },
                        get selectedDisplay() {
                            if (!this.selectedId) return 'Selecciona un territorio...';
                            let selected = this.territories.find(t => t.id == this.selectedId);
                            return selected ? selected.display : 'Selecciona un territorio...';
                        },
                        selectOption(id) {
                            this.selectedId = id;
                            this.open = false;
                            this.search = '';
                        }
                    }" @click.away="open = false">

                    <input type="hidden" name="territory_id" :value="selectedId" required>

                    <label
                        class="flex items-center gap-2 text-[13px] font-bold text-gray-700 dark:text-gray-300 mb-2 uppercase tracking-wide">
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 20l-5.447-2.724A2 2 0 013 15.485V5.118a2 2 0 011.056-1.747L9 0.618l5.447 2.724A2 2 0 0115 5.118v10.367a2 2 0 01-1.056 1.747L9 19.382z">
                            </path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20V0"></path>
                        </svg>
                        Territorio <span class="text-red-500">*</span>
                    </label>

                    <div class="relative">
                        <button type="button" @click="open = !open; if(open) $nextTick(() => $refs.searchInput.focus())"
                            class="w-full text-left px-4 py-2.5 bg-white dark:bg-gray-900 border border-gray-300 dark:border-gray-700 rounded-md text-gray-700 dark:text-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all flex justify-between items-center group shadow-sm">
                            <span x-text="selectedDisplay"
                                :class="{'text-gray-900 dark:text-white font-medium': selectedId}"></span>
                            <svg class="w-5 h-5 text-gray-400 group-hover:text-gray-600 dark:group-hover:text-gray-300 transition-colors"
                                :class="{'rotate-180': open}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>

                        <!-- Dropdown Menu -->
                        <div x-show="open" x-transition:enter="transition ease-out duration-100"
                            x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                            x-transition:leave="transition ease-in duration-75"
                            x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
                            class="absolute z-50 w-full mt-2 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg shadow-xl ring-1 ring-black ring-opacity-5 overflow-hidden"
                            style="display: none;">

                            <!-- Buscador -->
                            <div
                                class="p-2 border-b border-gray-100 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/50">
                                <div class="relative flex items-center">
                                    <div class="absolute left-3 text-gray-400 pointer-events-none">
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                        </svg>
                                    </div>
                                    <input type="text" x-model="search" x-ref="searchInput"
                                        placeholder="Buscar barrio o código..." style="padding-left: 2.25rem;"
                                        class="w-full pr-3 py-2 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-600 rounded-md text-sm text-gray-900 dark:text-white placeholder-gray-400 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                                </div>
                            </div>

                            <!-- Opciones -->
                            <ul class="max-h-60 overflow-y-auto p-1 custom-scrollbar">
                                <template x-for="territory in filteredTerritories" :key="territory.id">
                                    <li>
                                        <button type="button" @click="selectOption(territory.id)"
                                            class="w-full text-left px-3 py-2 rounded-md text-sm flex items-center justify-between transition-colors"
                                            :class="{'bg-blue-50 text-blue-700 dark:bg-blue-500/10 dark:text-blue-400 font-medium': selectedId === territory.id, 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700': selectedId !== territory.id}">
                                            <div class="flex flex-col">
                                                <span class="font-bold text-gray-900 dark:text-white"
                                                    x-text="territory.name + (territory.type === 'business' ? ' (Negocios)' : '')"></span>
                                                <span class="text-[11px] text-gray-500 dark:text-gray-400"
                                                    x-text="territory.code + (territory.city_name ? ' - ' + territory.city_name : '')"></span>
                                            </div>
                                            <svg x-show="selectedId === territory.id"
                                                class="w-4 h-4 text-blue-600 dark:text-blue-400 flex-shrink-0"
                                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M5 13l4 4L19 7"></path>
                                            </svg>
                                        </button>
                                    </li>
                                </template>

                                <!-- Mensaje Principal -->
                                <li x-show="search === ''"
                                    class="px-4 py-4 text-xs text-gray-500 dark:text-gray-400 text-center italic">
                                    Empieza a escribir para buscar un territorio...
                                </li>

                                <!-- No se encontró resultado -->
                                <li x-show="search !== '' && filteredTerritories.length === 0"
                                    class="px-4 py-4 text-xs text-gray-500 dark:text-gray-400 text-center italic">
                                    No se encontraron territorios.
                                </li>
                            </ul>
                        </div>
                    </div>
                    <x-input-error :messages="$errors->get('territory_id')" class="mt-2" />
                </div>

                <style>
                    .custom-scrollbar::-webkit-scrollbar {
                        width: 4px;
                    }

                    .custom-scrollbar::-webkit-scrollbar-track {
                        background: transparent;
                    }

                    .custom-scrollbar::-webkit-scrollbar-thumb {
                        background: #CBD5E1;
                        border-radius: 10px;
                    }

                    .dark .custom-scrollbar::-webkit-scrollbar-thumb {
                        background: #475569;
                    }
                </style>

                {{-- Map URL --}}
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">URL del Mapa
                        (Opcional)</label>
                    <input type="url" name="map_url" value="{{ old('map_url') }}"
                        placeholder="https://maps.google.com/..."
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-900 dark:border-gray-700 dark:text-gray-300">
                    <x-input-error :messages="$errors->get('map_url')" class="mt-2" />
                </div>

                {{-- Notes --}}
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Nota (Opcional)</label>
                    <textarea name="notes" rows="3"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-900 dark:border-gray-700 dark:text-gray-300">{{ old('notes') }}</textarea>
                    <x-input-error :messages="$errors->get('notes')" class="mt-2" />
                </div>

                <div class="flex items-center gap-4 pt-4 border-t border-gray-100 dark:border-gray-700">
                    <button type="submit"
                        class="inline-flex items-center justify-center h-11 px-8 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl font-black text-xs uppercase tracking-widest shadow-lg shadow-emerald-100 dark:shadow-none transition-all active:scale-95 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 leading-none whitespace-nowrap">
                        Registrar
                    </button>
                    <a href="{{ request('redirect_to', route('persons.index')) }}"
                        class="inline-flex items-center justify-center h-11 px-6 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl font-bold text-xs text-gray-500 hover:text-gray-900 dark:text-gray-400 dark:hover:text-gray-100 transition-all shadow-sm hover:shadow-md uppercase tracking-widest leading-none whitespace-nowrap">
                        Cancelar
                    </a>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>