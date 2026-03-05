<x-app-layout>
    <x-slot name="title">Asignar Territorio Personal</x-slot>
    <x-slot name="logo_url">{{ route('admin.personal-territories.index') }}</x-slot>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Asignar Territorio Personal') }}
        </h2>
    </x-slot>

    <div class="py-6 max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
            <form action="{{ route('admin.personal-territories.store') }}" method="POST" class="space-y-6">
                @csrf

                <!-- Publicador (Alpine Search) -->
                <div x-data="{ 
                    search: '', 
                    openUserModal() { $dispatch('open-modal', 'user-search-modal'); },
                    selectedUserId: '{{ old('user_id') }}',
                    selectedUserName: '{{ old('user_id') ? addslashes($users->firstWhere('id', old('user_id'))?->name ?? 'Seleccione un publicador') : 'Seleccione un publicador' }}',
                    selectedUserAvatar: '{{ old('user_id') ? $users->firstWhere('id', old('user_id'))?->profile_photo_url : '' }}'
                 }">
                    <label for="user_id"
                        class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Publicador</label>
                    <input type="hidden" name="user_id" x-model="selectedUserId" id="user_id">

                    <button type="button" @click="openUserModal"
                        class="mt-1 flex items-center justify-between w-full px-4 py-3 border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-700 dark:text-gray-300 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 text-left transition-all">
                        <div class="flex items-center gap-2 truncate">
                            <img x-show="selectedUserAvatar" :src="selectedUserAvatar" alt="Avatar"
                                class="w-7 h-7 rounded-full object-cover">
                            <span x-text="selectedUserName" class="truncate font-medium"></span>
                        </div>
                        <svg class="w-5 h-5 text-gray-400 flex-none" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                    @error('user_id') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror

                    <x-modal name="user-search-modal" maxWidth="md" focusable>
                        <div class="p-6">
                            <h2 class="text-lg font-bold text-gray-900 dark:text-gray-100 mb-4">
                                {{ __('Buscar Usuario') }}
                            </h2>
                            <x-text-input x-model="search" type="text" class="mt-1 block w-full mb-4"
                                placeholder="Escribe el nombre del usuario..." @keydown.enter.prevent="" />

                            <div class="max-h-60 overflow-y-auto w-full space-y-1 pr-2">
                                <div x-show="search === '' && !selectedUserId"
                                    class="text-center py-6 text-gray-500 dark:text-gray-400 text-sm">
                                    Empieza a escribir para buscar un usuario.
                                </div>

                                @foreach($users as $user)
                                    <button type="button"
                                        x-show="(search === '' && selectedUserId == '{{ $user->id }}') || (search !== '' && '{{ strtolower($user->name) }}'.includes(search.toLowerCase()))"
                                        @click="selectedUserId = '{{ $user->id }}'; selectedUserName = '{{ addslashes($user->name) }}'; selectedUserAvatar = '{{ $user->profile_photo_url }}'; $dispatch('close-modal', 'user-search-modal');"
                                        class="w-full flex items-center gap-3 text-left px-4 py-3 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg text-sm transition-colors"
                                        :class="{'bg-emerald-50 dark:bg-emerald-900/40 text-emerald-700 dark:text-emerald-400 font-bold': selectedUserId == '{{ $user->id }}'}">
                                        <img src="{{ $user->profile_photo_url }}" alt="Photo"
                                            class="w-10 h-10 rounded-full object-cover shadow-sm">
                                        <span>{{ $user->name }}</span>
                                    </button>
                                @endforeach

                                <div x-show="search !== '' && !Array.from($el.parentElement.querySelectorAll('button')).some(b => b.style.display !== 'none')"
                                    class="text-center py-4 text-gray-500 dark:text-gray-400 text-sm"
                                    style="display: none;">
                                    No se encontraron usuarios.
                                </div>
                            </div>

                            <div class="mt-6 flex justify-end">
                                <x-secondary-button x-on:click="$dispatch('close')">
                                    {{ __('Cancelar') }}
                                </x-secondary-button>
                            </div>
                        </div>
                    </x-modal>
                </div>

                <!-- Territorio (Dropdown Search - Exact same as Solicitar Territorio) -->
                @php
                    $territoriesData = $territories->map(function ($t) {
                        return [
                            'id' => $t->id,
                            'code' => $t->code,
                            'city_name' => $t->city ? $t->city->name : '',
                            'name' => $t->neighborhood_name ?? 'Sin barrio',
                            'display' => $t->code . ($t->city ? ' - ' . $t->city->name : '') . ' - ' . ($t->neighborhood_name ?? 'Sin barrio')
                        ];
                    })->values()->toJson();
                @endphp
                <div x-data="{
                        open: false,
                        search: '',
                        territories: {{ $territoriesData }},
                        selectedId: '{{ old('territory_id') }}',
                        get filteredTerritories() {
                            if (this.search === '') return []; 
                            let searchClean = this.search.toLowerCase().replace(/\s/g, '');
                            return this.territories.filter(t => 
                                t.code.toLowerCase().replace(/\s/g, '').includes(searchClean) || 
                                t.name.toLowerCase().includes(this.search.toLowerCase())
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
                    }" class="relative" @click.away="open = false">

                    <label for="territory_id" class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">
                        {{ __('Territorio (Disponibles)') }}
                    </label>
                    <input type="hidden" name="territory_id" :value="selectedId" required>

                    <button type="button" @click="open = !open; if(open) $nextTick(() => $refs.searchInput.focus())"
                        class="w-full text-left px-4 py-3 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-xl text-gray-700 dark:text-gray-300 focus:outline-none focus:ring-2 focus:ring-emerald-500 transition-all flex justify-between items-center group shadow-sm">
                        <span x-text="selectedDisplay"
                            :class="{'text-gray-900 dark:text-white font-medium': selectedId}"></span>
                        <svg class="w-5 h-5 text-gray-400 group-hover:text-gray-600 dark:group-hover:text-gray-300 transition-colors"
                            :class="{'rotate-180': open}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7">
                            </path>
                        </svg>
                    </button>

                    <div x-show="open" x-transition:enter="transition ease-out duration-100"
                        x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                        x-transition:leave="transition ease-in duration-75"
                        x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
                        class="absolute z-50 w-full mt-2 bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700 rounded-xl shadow-lg ring-1 ring-black ring-opacity-5 overflow-hidden"
                        style="display: none;">

                        <div class="p-2 border-b border-gray-100 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/50">
                            <div class="relative flex items-center">
                                <div class="absolute left-3 text-gray-400 pointer-events-none">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                    </svg>
                                </div>
                                <input type="text" x-model="search" x-ref="searchInput"
                                    placeholder="Buscar barrio o código..." style="padding-left: 2.25rem;"
                                    class="w-full pr-3 py-2 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-600 rounded-lg text-sm text-gray-900 dark:text-white placeholder-gray-400 focus:outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500">
                            </div>
                        </div>

                        <ul class="max-h-60 overflow-y-auto p-1 custom-scrollbar">
                            <template x-for="territory in filteredTerritories" :key="territory.id">
                                <li>
                                    <button type="button" @click="selectOption(territory.id)"
                                        class="w-full text-left px-3 py-2.5 rounded-lg text-sm flex items-center justify-between transition-colors"
                                        :class="{'bg-emerald-50 text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-400 font-medium': selectedId === territory.id, 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700': selectedId !== territory.id}">
                                        <div class="flex flex-col">
                                            <span class="font-bold text-gray-900 dark:text-white"
                                                x-text="territory.name"></span>
                                            <span class="text-xs text-gray-500 dark:text-gray-400"
                                                x-text="territory.code + (territory.city_name ? ' - ' + territory.city_name : '')"></span>
                                        </div>
                                        <svg x-show="selectedId === territory.id"
                                            class="w-4 h-4 text-emerald-600 dark:text-emerald-400 flex-shrink-0"
                                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M5 13l4 4L19 7"></path>
                                        </svg>
                                    </button>
                                </li>
                            </template>

                            <li x-show="search === ''"
                                class="px-4 py-4 text-sm text-gray-500 dark:text-gray-400 text-center">
                                Empieza a escribir para buscar un territorio...
                            </li>

                            <li x-show="search !== '' && filteredTerritories.length === 0"
                                class="px-4 py-4 text-sm text-gray-500 dark:text-gray-400 text-center">
                                No se encontraron territorios.
                            </li>
                        </ul>
                    </div>
                </div>
                @error('territory_id') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror

                <!-- Due Date -->
                <div>
                    <label for="due_date" class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Fecha
                        Límite (Opcional)</label>
                    <input type="text" id="due_date" name="due_date" value="{{ old('due_date') }}"
                        class="w-full px-4 py-3 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-xl text-gray-900 dark:text-white shadow-sm focus:outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 transition-colors">
                    <p class="mt-2 text-xs text-gray-500">La fecha hasta la cual el publicador debería completar el
                        territorio.</p>
                    @error('due_date') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                <div class="pt-6 border-t border-gray-100 dark:border-gray-700 flex gap-3">
                    <a href="{{ route('admin.personal-territories.index') }}"
                        class="btn-cancel-custom flex-1 flex justify-center py-3 px-4 border border-gray-300 dark:border-gray-600 rounded-xl font-bold transition-colors hover:bg-gray-50 dark:hover:bg-gray-700">
                        {{ __('Cancelar') }}
                    </a>
                    <button type="submit"
                        class="btn-submit-custom flex-[2] flex justify-center py-3 px-4 border border-transparent rounded-xl shadow-sm text-sm font-bold text-white transition-opacity hover:opacity-90 focus:outline-none focus:ring-2 focus:ring-emerald-500">
                        Asignar Territorio
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Flatpickr for Date Input -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://npmcdn.com/flatpickr/dist/l10n/es.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            flatpickr("#due_date", {
                locale: "es",
                altInput: true,
                altFormat: "d/m/Y",
                dateFormat: "Y-m-d",
                minDate: "today"
            });
        });
    </script>

    <style>
        .btn-cancel-custom {
            color: #1e3a8a;
            font-size: 15px;
        }

        .dark .btn-cancel-custom {
            color: #e2e8f0;
        }

        .btn-submit-custom {
            background-color: #0f9d58;
            font-size: 15px;
        }
    </style>
</x-app-layout>