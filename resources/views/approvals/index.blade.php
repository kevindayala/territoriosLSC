<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Aprobaciones Pendientes') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            {{-- Pending Persons --}}
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <h3 class="text-lg font-medium mb-4">Personas / Direcciones Pendientes</h3>
                    
                    @if($pendingPersons->isEmpty())
                        <p class="text-gray-500">No hay registros pendientes de aprobación.</p>
                    @else
                        <div class="overflow-x-auto w-full" x-data="{ openDiff: null, selectedPersonId: null }">
                            <table class="w-full min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                <thead class="bg-gray-50 dark:bg-gray-700">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Tipo</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Nombre / Dirección</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Territorio</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Registrado Por</th>
                                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                    @foreach($pendingPersons as $person)
                                        @php
                                            $isUpdate = !is_null($person->pending_changes);
                                        @endphp
                                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                @if($isUpdate)
                                                    <span class="px-2 py-1 text-[10px] font-bold uppercase rounded-full bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300">Actualización</span>
                                                @else
                                                    <span class="px-2 py-1 text-[10px] font-bold uppercase rounded-full bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300">Nueva Persona</span>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4">
                                                <div class="font-medium text-gray-900 dark:text-white">{{ $person->full_name }}</div>
                                                <div class="text-sm text-gray-500">{{ $person->address }}</div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                {{ $person->territory->code }} - {{ $person->territory->neighborhood_name }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                {{ $person->creator->name ?? 'N/A' }} 
                                                <br>
                                                <span class="text-xs">{{ $person->created_at->diffForHumans() }}</span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                                <div class="flex items-center justify-end gap-3">
                                                    @if($isUpdate)
                                                        <button @click="openDiff === {{ $person->id }} ? openDiff = null : openDiff = {{ $person->id }}" 
                                                                class="text-blue-600 hover:text-blue-900 font-bold underline">
                                                            Comparar
                                                        </button>
                                                    @endif

                                                    <form action="{{ route('approvals.approve', $person) }}" method="POST" class="inline-block">
                                                        @csrf
                                                        <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-3 py-1 rounded text-xs font-bold transition">Aprobar</button>
                                                    </form>
                                                    
                                                    <a href="{{ route('persons.edit', ['person' => $person, 'redirect_to' => 'approvals']) }}" class="text-gray-500 hover:text-gray-700" title="Editar manual">
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                                    </a>

                                                    <button type="button" 
                                                            @click="selectedPersonId = {{ $person->id }}; $dispatch('open-modal', 'confirm-reject')"
                                                            class="text-red-600 hover:text-red-900" title="Eliminar">
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                                    </button>
                                                </div>

                                                <form id="reject-form-{{ $person->id }}" action="{{ route('approvals.reject', $person) }}" method="POST" class="hidden">
                                                    @csrf
                                                </form>
                                            </td>
                                        </tr>

                                        {{-- Comparison Row --}}
                                        @if($isUpdate)
                                            <tr x-show="openDiff === {{ $person->id }}" x-cloak class="bg-blue-50/50 dark:bg-blue-900/10">
                                                <td colspan="5" class="px-6 py-4">
                                                    <div class="text-xs font-bold uppercase text-blue-600 dark:text-blue-400 mb-2">Comparación de Cambios</div>
                                                    <div class="grid grid-cols-2 gap-4 border dark:border-gray-700 rounded-lg overflow-hidden bg-white dark:bg-gray-900">
                                                        <div class="p-3 border-r dark:border-gray-700">
                                                            <div class="text-[10px] text-gray-400 uppercase font-black mb-2">Dato Actual (En el sistema)</div>
                                                    <table class="w-full text-sm">
                                                                @foreach($person->pending_changes as $key => $newValue)
                                                                    @php 
                                                                        $oldValue = $person->$key; 
                                                                        $displayKey = $key;
                                                                        $displayOld = $oldValue;
                                                                        $displayNew = $newValue;

                                                                        if ($key === 'territory_id') {
                                                                            $displayKey = 'Territorio';
                                                                            $oldTerr = $territories->firstWhere('id', $oldValue);
                                                                            $newTerr = $territories->firstWhere('id', $newValue);
                                                                            $displayOld = $oldTerr ? "$oldTerr->code - $oldTerr->neighborhood_name" : 'Sin territorio';
                                                                            $displayNew = $newTerr ? "$newTerr->code - $newTerr->neighborhood_name" : 'Sin territorio';
                                                                        } elseif ($key === 'full_name') {
                                                                            $displayKey = 'Nombre';
                                                                        } elseif ($key === 'address') {
                                                                            $displayKey = 'Dirección';
                                                                        } elseif ($key === 'map_url') {
                                                                            $displayKey = 'Mapa';
                                                                        } elseif ($key === 'notes') {
                                                                            $displayKey = 'Notas';
                                                                        } elseif ($key === 'status') {
                                                                            $displayKey = 'Estado';
                                                                            $displayOld = $oldValue == 'active' ? 'Activo' : ($oldValue == 'inactive' ? 'Inactivo' : 'Pendiente');
                                                                            $displayNew = $newValue == 'active' ? 'Activo' : ($newValue == 'inactive' ? 'Inactivo' : 'Pendiente');
                                                                        }
                                                                    @endphp
                                                                    @if($oldValue != $newValue)
                                                                        <tr class="border-b dark:border-gray-800 last:border-0 text-red-600 dark:text-red-400">
                                                                            <td class="py-1 font-bold w-24 align-top">{{ ucfirst($displayKey) }}:</td>
                                                                            <td class="py-1">{{ $displayOld }}</td>
                                                                        </tr>
                                                                    @endif
                                                                @endforeach
                                                            </table>
                                                        </div>
                                                        <div class="p-3 bg-green-50/30 dark:bg-green-900/10">
                                                            <div class="text-[10px] text-green-600 dark:text-green-400 uppercase font-black mb-2">Dato Nuevo (Pendiente)</div>
                                                            <table class="w-full text-sm">
                                                                @foreach($person->pending_changes as $key => $newValue)
                                                                    @php 
                                                                        $oldValue = $person->$key; 
                                                                        $displayKey = $key;
                                                                        $displayOld = $oldValue;
                                                                        $displayNew = $newValue;

                                                                        if ($key === 'territory_id') {
                                                                            $displayKey = 'Territorio';
                                                                            $oldTerr = $territories->firstWhere('id', $oldValue);
                                                                            $newTerr = $territories->firstWhere('id', $newValue);
                                                                            $displayOld = $oldTerr ? "$oldTerr->code - $oldTerr->neighborhood_name" : 'Sin territorio';
                                                                            $displayNew = $newTerr ? "$newTerr->code - $newTerr->neighborhood_name" : 'Sin territorio';
                                                                        } elseif ($key === 'full_name') {
                                                                            $displayKey = 'Nombre';
                                                                        } elseif ($key === 'address') {
                                                                            $displayKey = 'Dirección';
                                                                        } elseif ($key === 'map_url') {
                                                                            $displayKey = 'Mapa';
                                                                        } elseif ($key === 'notes') {
                                                                            $displayKey = 'Notas';
                                                                        } elseif ($key === 'status') {
                                                                            $displayKey = 'Estado';
                                                                            $displayOld = $oldValue == 'active' ? 'Activo' : ($oldValue == 'inactive' ? 'Inactivo' : 'Pendiente');
                                                                            $displayNew = $newValue == 'active' ? 'Activo' : ($newValue == 'inactive' ? 'Inactivo' : 'Pendiente');
                                                                        }
                                                                    @endphp
                                                                    @if($oldValue != $newValue)
                                                                        <tr class="border-b dark:border-gray-800 last:border-0 text-green-700 dark:text-green-300">
                                                                            <td class="py-1 font-bold w-24 align-top">{{ ucfirst($displayKey) }}:</td>
                                                                            <td class="py-1">{{ $displayNew }}</td>
                                                                        </tr>
                                                                    @endif
                                                                @endforeach
                                                            </table>
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endif
                                    @endforeach
                                </tbody>
                            </table>
                            
                            {{-- Confirmation Modal --}}
                            <x-modal-confirm name="confirm-reject" title="Rechazar/Eliminar"
                                content="¿Estás seguro de que deseas eliminar este registro? Esta acción es definitiva y no se puede deshacer.">
                                <x-slot name="footer">
                                    <x-secondary-button x-on:click="$dispatch('close')">
                                        Cancelar
                                    </x-secondary-button>

                                    <x-danger-button class="ml-3" x-on:click="document.getElementById('reject-form-' + selectedPersonId).submit()">
                                        Sí, eliminar
                                    </x-danger-button>
                                </x-slot>
                            </x-modal-confirm>

                        </div>
                        <div class="mt-4">
                            {{ $pendingPersons->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
