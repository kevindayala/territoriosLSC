@csrf

<div class="space-y-6">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div x-data="{ 
            search: '', 
            openTerritoryModal() { $dispatch('open-modal', 'territory-search-modal'); },
            selectedTerritoryId: '{{ old('territory_id', $registro->territory_id ?? '') }}',
            selectedTerritoryDisplay: '{{ old('territory_id', $registro->territory_id ?? '') ? addslashes(($t = $territories->firstWhere('id', old('territory_id', $registro->territory_id ?? ''))) ? $t->code . ($t->city ? ' - ' . $t->city->name : '') . ' - ' . ($t->neighborhood_name ?? 'Sin barrio') : 'Seleccione un territorio') : 'Seleccione un territorio' }}'
         }">
            <x-input-label for="territory_id" :value="__('Territorio')"
                class="text-sm font-bold text-gray-700 dark:text-gray-300" />
            <input type="hidden" name="territory_id" x-model="selectedTerritoryId" id="territory_id">

            <button type="button" @click="openTerritoryModal"
                class="mt-1 flex items-center justify-between w-full px-3 py-2 border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-700 dark:text-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-left">
                <div class="flex items-center gap-2 truncate">
                    <span x-text="selectedTerritoryDisplay" class="truncate font-medium"></span>
                </div>
                <svg class="w-5 h-5 text-gray-400 flex-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                </svg>
            </button>
            <x-input-error class="mt-2 text-sm text-red-600" :messages="$errors->get('territory_id')" />

            <x-modal name="territory-search-modal" maxWidth="md" focusable>
                <div class="p-6">
                    <h2 class="text-lg font-bold text-gray-900 dark:text-gray-100 mb-4">
                        {{ __('Buscar Territorio') }}
                    </h2>
                    <x-text-input x-model="search" type="text" class="mt-1 block w-full mb-4"
                        placeholder="Escribe el código o barrio..." @keydown.enter.prevent="" />

                    <div class="max-h-60 overflow-y-auto w-full space-y-1 pr-2">
                        <div x-show="search === '' && !selectedTerritoryId"
                            class="text-center py-6 text-gray-500 dark:text-gray-400 text-sm">
                            Empieza a escribir para buscar un territorio.
                        </div>

                        @foreach($territories as $territory)
                            @php
                                $display = addslashes($territory->code . ($territory->city ? ' - ' . $territory->city->name : '') . ' - ' . ($territory->neighborhood_name ?? 'Sin barrio'));
                                $searchString = strtolower(addslashes($territory->code . ' ' . ($territory->neighborhood_name ?? $territory->city?->name ?? '')));
                            @endphp
                            <button type="button"
                                x-show="(search === '' && selectedTerritoryId == '{{ $territory->id }}') || (search !== '' && '{{ $searchString }}'.includes(search.toLowerCase()))"
                                @click="selectedTerritoryId = '{{ $territory->id }}'; selectedTerritoryDisplay = '{{ $display }}'; $dispatch('close-modal', 'territory-search-modal');"
                                class="w-full flex items-center gap-3 text-left px-4 py-3 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg text-sm transition-colors text-gray-800 dark:text-gray-200"
                                :class="{'bg-blue-50 dark:bg-blue-900/30 font-bold text-blue-700 dark:text-blue-400': selectedTerritoryId == '{{ $territory->id }}'}">
                                <div class="flex flex-col py-0.5">
                                    <span class="font-bold text-gray-900 dark:text-gray-100">
                                        {{ $territory->neighborhood_name ?? 'Sin barrio' }}
                                    </span>
                                    <span class="text-xs text-gray-500 dark:text-gray-400">
                                        {{ $territory->code }}{{ $territory->city ? ' - ' . $territory->city->name : '' }}
                                    </span>
                                </div>
                            </button>
                        @endforeach

                        <div x-show="search !== '' && !Array.from($el.parentElement.querySelectorAll('button')).some(b => b.style.display !== 'none')"
                            class="text-center py-4 text-gray-500 dark:text-gray-400 text-sm" style="display: none;">
                            No se encontraron territorios.
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

        <div x-data="{ 
            search: '', 
            openUserModal() { $dispatch('open-modal', 'user-search-modal'); },
            selectedUserId: '{{ old('assigned_to_user_id', $registro->assigned_to_user_id ?? '') }}',
            selectedUserName: '{{ old('assigned_to_user_id', $registro->assigned_to_user_id ?? '') ? addslashes($users->firstWhere('id', old('assigned_to_user_id', $registro->assigned_to_user_id ?? ''))?->name ?? 'Seleccione un usuario') : 'Seleccione un usuario' }}',
            selectedUserAvatar: '{{ old('assigned_to_user_id', $registro->assigned_to_user_id ?? '') ? $users->firstWhere('id', old('assigned_to_user_id', $registro->assigned_to_user_id ?? ''))?->profile_photo_url : '' }}'
         }">
            <x-input-label for="assigned_to_user_id" :value="__('Usuario Asignado')"
                class="text-sm font-bold text-gray-700 dark:text-gray-300" />
            <input type="hidden" name="assigned_to_user_id" x-model="selectedUserId" id="assigned_to_user_id">

            <button type="button" @click="openUserModal"
                class="mt-1 flex items-center justify-between w-full px-3 py-2 border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-700 dark:text-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-left">
                <div class="flex items-center gap-2 truncate">
                    <img x-show="selectedUserAvatar" :src="selectedUserAvatar" alt="Avatar"
                        class="w-6 h-6 rounded-full object-cover">
                    <span x-text="selectedUserName" class="truncate"></span>
                </div>
                <svg class="w-5 h-5 text-gray-400 flex-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                </svg>
            </button>
            <x-input-error class="mt-2 text-sm text-red-600" :messages="$errors->get('assigned_to_user_id')" />

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
                                x-show="(search === '' && selectedUserId == '{{ $user->id }}') || (search !== '' && '{{ strtolower(addslashes($user->name)) }}'.includes(search.toLowerCase()))"
                                @click="selectedUserId = '{{ $user->id }}'; selectedUserName = '{{ addslashes($user->name) }}'; selectedUserAvatar = '{{ $user->profile_photo_url }}'; $dispatch('close-modal', 'user-search-modal');"
                                class="w-full flex items-center gap-3 text-left px-4 py-3 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg text-sm transition-colors text-gray-800 dark:text-gray-200"
                                :class="{'bg-blue-50 dark:bg-blue-900/30 font-bold text-blue-700 dark:text-blue-400': selectedUserId == '{{ $user->id }}'}">
                                <img src="{{ $user->profile_photo_url }}" alt="Avatar de {{ $user->name }}"
                                    class="w-8 h-8 rounded-full object-cover">
                                <span>{{ $user->name }}</span>
                            </button>
                        @endforeach

                        <div x-show="search !== '' && !Array.from($el.parentElement.querySelectorAll('button')).some(b => b.style.display !== 'none')"
                            class="text-center py-4 text-gray-500 dark:text-gray-400 text-sm" style="display: none;">
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

        <div>
            <x-input-label for="type" :value="__('Tipo de Asignación')"
                class="text-sm font-bold text-gray-700 dark:text-gray-300" />
            <select id="type" name="type"
                class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-blue-500 dark:focus:border-blue-600 focus:ring-blue-500 dark:focus:ring-blue-600 rounded-md shadow-sm"
                required>
                <option value="regular" {{ old('type', $registro->type ?? '') == 'regular' ? 'selected' : '' }}>Territorio
                    regular</option>
                <option value="personal" {{ old('type', $registro->type ?? '') == 'personal' ? 'selected' : '' }}>
                    Territorio personal</option>
            </select>
            <x-input-error class="mt-2 text-sm text-red-600" :messages="$errors->get('type')" />
        </div>

        <div>
            <x-input-label for="assigned_at" :value="__('Fecha de Asignación')"
                class="text-sm font-bold text-gray-700 dark:text-gray-300" />
            <x-text-input id="assigned_at" name="assigned_at" type="text" class="mt-1 block w-full"
                :value="old('assigned_at', isset($registro) && $registro->assigned_at ? $registro->assigned_at->format('Y-m-d') : date('Y-m-d'))" required />
            <x-input-error class="mt-2 text-sm text-red-600" :messages="$errors->get('assigned_at')" />
        </div>

        <div>
            <x-input-label for="completed_at" :value="__('Fecha de Finalización (Dejar vacío si no está terminado)')"
                class="text-sm font-bold text-gray-700 dark:text-gray-300" />
            <x-text-input id="completed_at" name="completed_at" type="text" class="mt-1 block w-full"
                :value="old('completed_at', isset($registro) && $registro->completed_at ? $registro->completed_at->format('Y-m-d') : null)" />
            <x-input-error class="mt-2 text-sm text-red-600" :messages="$errors->get('completed_at')" />
        </div>
    </div>
</div>

<div class="flex items-center justify-end mt-8 border-t border-gray-100 dark:border-gray-700 pt-6 gap-4">
    <a href="{{ route('admin.registros.index') }}"
        class="btn-cancel-custom inline-flex items-center px-6 py-2.5 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-xl font-medium shadow-sm hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
        {{ __('Cancelar') }}
    </a>
    <button type="submit"
        class="btn-submit-custom inline-flex items-center gap-2 px-6 py-2.5 border border-transparent rounded-xl font-medium text-white focus:outline-none focus:ring-2 focus:ring-offset-2 transition ease-in-out duration-150 shadow-sm hover:opacity-90">
        <span>{{ isset($registro) ? __('Actualizar') : __('Guardar') }}</span>
    </button>
</div>

<!-- Flatpickr for Date Input -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="https://npmcdn.com/flatpickr/dist/l10n/es.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        flatpickr("#assigned_at", {
            locale: "es",
            altInput: true,
            altFormat: "d/m/Y",
            dateFormat: "Y-m-d",
        });
        flatpickr("#completed_at", {
            locale: "es",
            altInput: true,
            altFormat: "d/m/Y",
            dateFormat: "Y-m-d",
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