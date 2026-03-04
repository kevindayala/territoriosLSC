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
                    <label class="flex items-center gap-2 text-[13px] font-bold text-gray-700 dark:text-gray-300 mb-2 uppercase tracking-wide">
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                        Nombre Completo
                    </label>
                    <input type="text" name="full_name" value="{{ old('full_name') }}" required
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-900 dark:border-gray-700 dark:text-gray-300">
                    <x-input-error :messages="$errors->get('full_name')" class="mt-2" />
                </div>

                {{-- Address --}}
                <div class="mb-5">
                    <label class="flex items-center gap-2 text-[13px] font-bold text-gray-700 dark:text-gray-300 mb-2 uppercase tracking-wide">
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                        Dirección
                    </label>
                    <textarea name="address" rows="3" required
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-900 dark:border-gray-700 dark:text-gray-300">{{ old('address') }}</textarea>
                    <x-input-error :messages="$errors->get('address')" class="mt-2" />
                </div>

                {{-- City (Dependency for Territory) --}}
                <div class="mb-5">
                    <label class="flex items-center gap-2 text-[13px] font-bold text-gray-700 dark:text-gray-300 mb-2 uppercase tracking-wide">
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                        Ciudad
                    </label>
                    <select id="city_selector"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-900 dark:border-gray-700 dark:text-gray-300 cursor-pointer">
                        <option value="">Seleccione una ciudad...</option>
                        @foreach($cities as $city)
                            <option value="{{ $city->id }}" {{ (isset($initialTerritory) && $initialTerritory->city_id == $city->id) ? 'selected' : '' }}>
                                {{ $city->display_name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Territory --}}
                <div class="mb-5">
                    <label class="flex items-center gap-2 text-[13px] font-bold text-gray-700 dark:text-gray-300 mb-2 uppercase tracking-wide">
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A2 2 0 013 15.485V5.118a2 2 0 011.056-1.747L9 0.618l5.447 2.724A2 2 0 0115 5.118v10.367a2 2 0 01-1.056 1.747L9 19.382z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20V0"></path></svg>
                        Territorio
                    </label>
                    <select name="territory_id" id="territory_id" required {{ !isset($initialTerritory) ? 'disabled' : '' }}
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-900 dark:border-gray-700 dark:text-gray-300 {{ !isset($initialTerritory) ? 'bg-gray-100 cursor-not-allowed' : 'cursor-pointer' }}">
                        <option value="">{{ !isset($initialTerritory) ? 'Primero seleccione una ciudad...' : 'Seleccione un territorio...' }}</option>
                        @foreach($territories as $territory)
                            @if(!isset($initialTerritory) || $territory->city_id == $initialTerritory->city_id)
                                <option value="{{ $territory->id }}" data-city="{{ $territory->city_id }}"
                                    {{ (isset($initialTerritory) && $initialTerritory->id == $territory->id) ? 'selected' : '' }}>
                                    {{ $territory->code }} - {{ $territory->neighborhood_name }}
                                </option>
                            @endif
                        @endforeach
                    </select>
                    <x-input-error :messages="$errors->get('territory_id')" class="mt-2" />
                </div>

                <script>
                    document.addEventListener('DOMContentLoaded', function () {
                        const citySelector = document.getElementById('city_selector');
                        const territorySelector = document.getElementById('territory_id');
                        // Store all original options to filter from
                        const allTerritories = {!! json_encode($territories->map(function($t) { 
                            return ['id' => $t->id, 'code' => $t->code, 'neighborhood_name' => $t->neighborhood_name, 'city_id' => $t->city_id]; 
                        })->toArray()) !!};                        
                        const initialTerritoryId = "{{ $initialTerritory->id ?? '' }}";

                        function filterTerritories() {
                            const selectedCityId = citySelector.value;

                            // Reset territory selection
                            territorySelector.value = '';

                            if (!selectedCityId) {
                                territorySelector.disabled = true;
                                territorySelector.classList.add('bg-gray-100', 'cursor-not-allowed');
                                territorySelector.innerHTML = '<option value="">Primero seleccione una ciudad...</option>';
                                return;
                            }

                            // Enable select
                            territorySelector.disabled = false;
                            territorySelector.classList.remove('bg-gray-100', 'cursor-not-allowed');
                            territorySelector.classList.add('cursor-pointer');
                            territorySelector.innerHTML = '<option value="">Seleccione un territorio...</option>';

                            // Filter and append matching options
                            const filtered = allTerritories.filter(t => t.city_id == selectedCityId);

                            if (filtered.length === 0) {
                                territorySelector.innerHTML = '<option value="">No hay territorios en esta ciudad</option>';
                            } else {
                                filtered.forEach(t => {
                                    const option = document.createElement('option');
                                    option.value = t.id;
                                    option.text = `${t.code} - ${t.neighborhood_name}`;
                                    option.dataset.city = t.city_id;
                                    if (t.id == initialTerritoryId) option.selected = true;
                                    territorySelector.appendChild(option);
                                });
                            }
                        }

                        citySelector.addEventListener('change', filterTerritories);
                    });
                </script>

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