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

                {{-- Full Name --}}
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Nombre Completo</label>
                    <input type="text" name="full_name" value="{{ old('full_name') }}" required
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-900 dark:border-gray-700 dark:text-gray-300">
                    <x-input-error :messages="$errors->get('full_name')" class="mt-2" />
                </div>

                {{-- Address --}}
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Dirección</label>
                    <textarea name="address" rows="3" required
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-900 dark:border-gray-700 dark:text-gray-300">{{ old('address') }}</textarea>
                    <x-input-error :messages="$errors->get('address')" class="mt-2" />
                </div>

                {{-- City (Dependency for Territory) --}}
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Ciudad</label>
                    <select id="city_selector"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-900 dark:border-gray-700 dark:text-gray-300">
                        <option value="">Seleccione una ciudad...</option>
                        @foreach($cities as $city)
                            <option value="{{ $city->id }}">{{ $city->name }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Territory --}}
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Territorio</label>
                    <select name="territory_id" id="territory_id" required disabled
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-900 dark:border-gray-700 dark:text-gray-300 bg-gray-100 cursor-not-allowed">
                        <option value="">Primero seleccione una ciudad...</option>
                        @foreach($territories as $territory)
                            <option value="{{ $territory->id }}" data-city="{{ $territory->city_id }}">
                                {{ $territory->code }} - {{ $territory->neighborhood_name }}
                            </option>
                        @endforeach
                    </select>
                    <x-input-error :messages="$errors->get('territory_id')" class="mt-2" />
                </div>

                <script>
                    document.addEventListener('DOMContentLoaded', function () {
                        const citySelector = document.getElementById('city_selector');
                        const territorySelector = document.getElementById('territory_id');
                        // Store all original options to filter from
                        const allTerritories = Array.from(territorySelector.options).slice(1); // skip placeholder

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
                            territorySelector.innerHTML = '<option value="">Seleccione un territorio...</option>';

                            // Filter and append matching options
                            const filtered = allTerritories.filter(option => option.dataset.city === selectedCityId);

                            if (filtered.length === 0) {
                                territorySelector.innerHTML = '<option value="">No hay territorios en esta ciudad</option>';
                            } else {
                                filtered.forEach(option => territorySelector.appendChild(option));
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

                <div class="flex items-center gap-4">
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                        Guardar
                    </button>
                    <a href="{{ route('persons.index') }}"
                        class="text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-gray-100">Cancelar</a>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>