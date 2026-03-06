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
                                    <svg class="h-6 w-6 text-amber-500" fill="none" viewBox="0 0 24 24"
                                        stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                    </svg>
                                </div>
                                <div class="flex-1">
                                    <h4 class="font-black text-sm uppercase tracking-wide text-amber-900">Revisión en curso
                                    </h4>
                                    <p class="mt-1 text-sm leading-relaxed text-amber-800/90">
                                        Este registro ya tiene cambios propuestos por
                                        <strong>"{{ $person->pendingUser->name ?? 'otro usuario' }}"</strong>.
                                        No puedes realizar ediciones hasta que el administrador procese la solicitud actual.
                                    </p>
                                </div>
                            </div>
                        </div>
                    @elseif($isOwner && $hasPending)
                        <div class="mb-6 p-4 bg-blue-50 border border-blue-200 text-blue-800 rounded-xl shadow-sm">
                            <div class="flex items-start gap-4">
                                <div class="flex-shrink-0 mt-0.5">
                                    <svg class="h-6 w-6 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                        stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                                <div class="flex-1">
                                    <h4 class="font-black text-sm uppercase tracking-wide text-blue-900">Tu revisión está
                                        pendiente</h4>
                                    <p class="mt-1 text-sm leading-relaxed text-blue-800/90">
                                        Ya tienes una propuesta de cambio enviada. Si guardas ahora, se **actualizará tu
                                        revisión** anterior con la nueva información.
                                    </p>
                                </div>
                            </div>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('persons.update', $person) }}">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="redirect_to"
                            value="{{ request('redirect_to', route('persons.index')) }}">

                        <fieldset @if($showBlock) disabled @endif
                            class="{{ $showBlock ? 'opacity-60 cursor-not-allowed' : '' }}">
                            {{-- Full Name --}}
                            <div class="mb-4">
                                <x-input-label for="full_name" :value="__('Nombre Completo')" />
                                <x-text-input id="full_name" class="block mt-1 w-full" type="text" name="full_name"
                                    :value="old('full_name', $displayData->full_name)" required />
                                <x-input-error :messages="$errors->get('full_name')" class="mt-2" />
                            </div>

                            {{-- Address --}}
                            <div class="mb-4">
                                <x-input-label for="address" :value="__('Dirección')" />
                                <textarea name="address" id="address" rows="3" required
                                    class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">{{ old('address', $displayData->address) }}</textarea>
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

                                $oldTerritoryId = old('territory_id', $displayData->territory_id);
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

                                <x-input-label for="territory_search" :value="__('Territorio')" />

                                <div class="relative mt-1">
                                    <button type="button"
                                        @click="open = !open; if(open) $nextTick(() => $refs.searchInput.focus())"
                                        class="w-full text-left px-4 py-2 bg-white dark:bg-gray-900 border border-gray-300 dark:border-gray-700 rounded-md text-gray-700 dark:text-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 transition-all flex justify-between items-center group shadow-sm">
                                        <span x-text="selectedDisplay"
                                            :class="{'text-gray-900 dark:text-white font-medium': selectedId}"></span>
                                        <svg class="w-5 h-5 text-gray-400 group-hover:text-gray-600 dark:group-hover:text-gray-300 transition-colors"
                                            :class="{'rotate-180': open}" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 9l-7 7-7-7"></path>
                                        </svg>
                                    </button>

                                    <!-- Dropdown Menu -->
                                    <div x-show="open" x-transition:enter="transition ease-out duration-100"
                                        x-transition:enter-start="opacity-0 scale-95"
                                        x-transition:enter-end="opacity-100 scale-100"
                                        x-transition:leave="transition ease-in duration-75"
                                        x-transition:leave-start="opacity-100 scale-100"
                                        x-transition:leave-end="opacity-0 scale-95"
                                        class="absolute z-50 w-full mt-2 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg shadow-xl ring-1 ring-black ring-opacity-5 overflow-hidden"
                                        style="display: none;">

                                        <!-- Buscador -->
                                        <div
                                            class="p-2 border-b border-gray-100 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/50">
                                            <div class="relative flex items-center">
                                                <div class="absolute left-3 text-gray-400 pointer-events-none">
                                                    <svg class="h-4 w-4" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                                    </svg>
                                                </div>
                                                <input type="text" x-model="search" x-ref="searchInput"
                                                    placeholder="Buscar barrio o código..."
                                                    style="padding-left: 2.25rem;"
                                                    class="w-full pr-3 py-2 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-600 rounded-md text-sm text-gray-900 dark:text-white placeholder-gray-400 focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500">
                                            </div>
                                        </div>

                                        <!-- Opciones -->
                                        <ul class="max-h-60 overflow-y-auto p-1 custom-scrollbar">
                                            <template x-for="territory in filteredTerritories" :key="territory.id">
                                                <li>
                                                    <button type="button" @click="selectOption(territory.id)"
                                                        class="w-full text-left px-3 py-2 rounded-md text-sm flex items-center justify-between transition-colors"
                                                        :class="{'bg-indigo-50 text-indigo-700 dark:bg-indigo-500/10 dark:text-indigo-400 font-medium': selectedId === territory.id, 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700': selectedId !== territory.id}">
                                                        <div class="flex flex-col">
                                                            <span class="font-bold text-gray-900 dark:text-white"
                                                                x-text="territory.name + (territory.type === 'business' ? ' (Negocios)' : '')"></span>
                                                            <span class="text-[11px] text-gray-500 dark:text-gray-400"
                                                                x-text="territory.code + (territory.city_name ? ' - ' + territory.city_name : '')"></span>
                                                        </div>
                                                        <svg x-show="selectedId === territory.id"
                                                            class="w-4 h-4 text-indigo-600 dark:text-indigo-400 flex-shrink-0"
                                                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2" d="M5 13l4 4L19 7"></path>
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
                                    <x-text-input id="map_url" class="block mt-1 w-full" type="url" name="map_url"
                                        :value="old('map_url', $displayData->map_url)"
                                        placeholder="https://maps.google.com/..." />
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
                            <button type="submit" {{ $showBlock ? 'disabled' : '' }}
                                class="inline-flex items-center justify-center h-11 px-8 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl font-black text-xs uppercase tracking-widest shadow-lg shadow-emerald-100 dark:shadow-none transition-all focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 active:scale-95 disabled:opacity-50 disabled:cursor-not-allowed leading-none whitespace-nowrap">
                                {{ $showBlock ? __('Bloqueado') : __('Actualizar') }}
                            </button>

                            @php
                                $backUrl = route('persons.index');
                                if (request('redirect_to') === 'approvals') {
                                    $backUrl = route('approvals.index');
                                } elseif (request('redirect_to')) {
                                    $backUrl = request('redirect_to');
                                }
                            @endphp
                            <a href="{{ $backUrl }}"
                                class="inline-flex items-center justify-center h-11 px-6 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl font-bold text-xs text-gray-500 hover:text-gray-900 dark:text-gray-400 dark:hover:text-gray-100 transition-all shadow-sm hover:shadow-md uppercase tracking-widest focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 leading-none whitespace-nowrap">
                                {{ __('Cancelar') }}
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>