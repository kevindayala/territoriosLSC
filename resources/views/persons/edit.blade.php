<x-app-layout>
    <x-slot name="logo_url">
        @if(auth()->user()->hasRole('admin') && $person->status === 'inactive')
            {{ route('admin.persons.inactive') }}
        @else
            {{ route('persons.index') }}
        @endif
    </x-slot>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Editar Persona') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    
                    @php
                        $hasPending = !is_null($person->pending_changes);
                        $isAdmin = auth()->user()->hasRole('admin');
                        $isOwner = $person->pending_by_user_id === auth()->id();
                        $showBlock = !$isAdmin && $hasPending && !$isOwner;
                        
                        // Use pending data if available, otherwise use live data
                        $displayData = $hasPending ? (object) array_merge($person->toArray(), $person->pending_changes) : $person;

                        // Safely get the city ID for the current territory
                        $displayTerritoryId = $displayData->territory_id;
                        $displayTerritory = $territories->firstWhere('id', $displayTerritoryId);
                        $displayCityId = $displayTerritory ? $displayTerritory->city_id : null;
                    @endphp

                    @if($showBlock)
                        <div class="mb-6 p-4 bg-amber-50 border border-amber-200 text-amber-800 rounded-xl shadow-sm">
                            <div class="flex items-start gap-4">
                                <div class="flex-shrink-0 mt-0.5">
                                    <svg class="h-6 w-6 text-amber-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                    </svg>
                                </div>
                                <div class="flex-1">
                                    <h4 class="font-black text-sm uppercase tracking-wide text-amber-900">Revisión en curso</h4>
                                    <p class="mt-1 text-sm leading-relaxed text-amber-800/90">
                                        Este registro ya tiene cambios propuestos por <strong>"{{ $person->pendingUser->name ?? 'otro usuario' }}"</strong>. 
                                        No puedes realizar ediciones hasta que el administrador procese la solicitud actual.
                                    </p>
                                </div>
                            </div>
                        </div>
                    @elseif($isOwner && $hasPending)
                        <div class="mb-6 p-4 bg-blue-50 border border-blue-200 text-blue-800 rounded-xl shadow-sm">
                            <div class="flex items-start gap-4">
                                <div class="flex-shrink-0 mt-0.5">
                                    <svg class="h-6 w-6 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                                <div class="flex-1">
                                    <h4 class="font-black text-sm uppercase tracking-wide text-blue-900">Tu revisión está pendiente</h4>
                                    <p class="mt-1 text-sm leading-relaxed text-blue-800/90">
                                        Ya tienes una propuesta de cambio enviada. Si guardas ahora, se **actualizará tu revisión** anterior con la nueva información.
                                    </p>
                                </div>
                            </div>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('persons.update', $person) }}">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="redirect_to" value="{{ request('redirect_to', 'persons') }}">

                        <fieldset @if($showBlock) disabled @endif class="{{ $showBlock ? 'opacity-60 cursor-not-allowed' : '' }}">
                            {{-- Full Name --}}
                            <div class="mb-4">
                                <x-input-label for="full_name" :value="__('Nombre Completo')" />
                                <x-text-input id="full_name" class="block mt-1 w-full" type="text" name="full_name" :value="old('full_name', $displayData->full_name)" required />
                                <x-input-error :messages="$errors->get('full_name')" class="mt-2" />
                            </div>

                            {{-- Address --}}
                            <div class="mb-4">
                                <x-input-label for="address" :value="__('Dirección')" />
                                <textarea name="address" id="address" rows="3" required
                                    class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">{{ old('address', $displayData->address) }}</textarea>
                                <x-input-error :messages="$errors->get('address')" class="mt-2" />
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                {{-- City --}}
                                <div>
                                    <x-input-label for="city_selector" :value="__('Ciudad')" />
                                    <select id="city_selector"
                                        class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                                        <option value="">Seleccione una ciudad...</option>
                                        @foreach($cities as $city)
                                            <option value="{{ $city->id }}" {{ $displayCityId == $city->id ? 'selected' : '' }}>
                                                {{ $city->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                {{-- Territory --}}
                                <div>
                                    <x-input-label for="territory_id" :value="__('Territorio')" />
                                    <select name="territory_id" id="territory_id" required
                                        class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                                        <option value="">Seleccione...</option>
                                        @foreach($territories as $territory)
                                            <option value="{{ $territory->id }}" 
                                                data-city="{{ $territory->city_id }}"
                                                {{ old('territory_id', $displayData->territory_id) == $territory->id ? 'selected' : '' }}>
                                                {{ $territory->code }} - {{ $territory->neighborhood_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <x-input-error :messages="$errors->get('territory_id')" class="mt-2" />
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                {{-- Status --}}
                                <div>
                                    <x-input-label for="status" :value="__('Estado')" />
                                    <select name="status" id="status" required
                                        class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                                        <option value="active" {{ old('status', $displayData->status) == 'active' ? 'selected' : '' }}>Activo</option>
                                        <option value="pending" {{ old('status', $displayData->status) == 'pending' ? 'selected' : '' }}>Pendiente</option>
                                        <option value="inactive" {{ old('status', $displayData->status) == 'inactive' ? 'selected' : '' }}>Inactivo</option>
                                    </select>
                                    <x-input-error :messages="$errors->get('status')" class="mt-2" />
                                </div>

                                {{-- Map URL --}}
                                <div>
                                    <x-input-label for="map_url" :value="__('URL del Mapa (Opcional)')" />
                                    <x-text-input id="map_url" class="block mt-1 w-full" type="url" name="map_url" :value="old('map_url', $displayData->map_url)" placeholder="https://maps.google.com/..." />
                                    <x-input-error :messages="$errors->get('map_url')" class="mt-2" />
                                </div>
                            </div>

                            {{-- Notes --}}
                            <div class="mb-6">
                                <x-input-label for="notes" :value="__('Notas (Opcional)')" />
                                <textarea name="notes" id="notes" rows="3"
                                    class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">{{ old('notes', $displayData->notes) }}</textarea>
                                <x-input-error :messages="$errors->get('notes')" class="mt-2" />
                            </div>
                        </fieldset>

                        <div class="flex items-center gap-4">
                            <x-primary-button :disabled="$showBlock" class="{{ $showBlock ? 'opacity-50 cursor-not-allowed' : '' }}">
                                {{ $showBlock ? __('Bloqueado por Revisión') : __('Actualizar') }}
                            </x-primary-button>
                            
                            <a href="{{ request('redirect_to') === 'approvals' ? route('approvals.index') : route('persons.index') }}"
                                class="inline-flex items-center px-4 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-500 rounded-md font-semibold text-xs text-gray-700 dark:text-gray-300 uppercase tracking-widest shadow-sm hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 disabled:opacity-25 transition ease-in-out duration-150">
                                {{ __('Volver') }}
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const citySelector = document.getElementById('city_selector');
            const territorySelector = document.getElementById('territory_id');
            const allTerritories = Array.from(territorySelector.options).slice(1);
            const currentTerritoryId = "{{ old('territory_id', $displayData->territory_id) }}";

            function filterTerritories() {
                const selectedCityId = citySelector.value;
                territorySelector.innerHTML = '<option value="">Seleccione un territorio...</option>';
                
                if (!selectedCityId) {
                    territorySelector.disabled = true;
                    return;
                }

                territorySelector.disabled = false;
                const filtered = allTerritories.filter(option => option.dataset.city === selectedCityId);
                
                if (filtered.length === 0) {
                    territorySelector.innerHTML = '<option value="">No hay territorios...</option>';
                } else {
                    filtered.forEach(option => {
                        territorySelector.appendChild(option);
                        if (option.value == currentTerritoryId) {
                            option.selected = true;
                        }
                    });
                }
            }

            filterTerritories();
            citySelector.addEventListener('change', function() {
                territorySelector.value = ''; 
                filterTerritories();
            });
        });
    </script>
</x-app-layout>