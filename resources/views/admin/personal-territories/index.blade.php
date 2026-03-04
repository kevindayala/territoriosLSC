<x-app-layout>
    <x-slot name="title">Territorios Personales</x-slot>
    <x-slot name="logo_url">{{ route('admin.settings') }}</x-slot>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Territorios Personales') }}
            </h2>
            <a href="{{ route('admin.personal-territories.create') }}"
                class="inline-flex items-center justify-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white border border-blue-600 rounded-lg text-sm font-bold shadow-sm transition-all focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                <svg class="w-4 h-4 me-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Asignar Nuevo
            </a>
        </div>
    </x-slot>

    <div class="py-6 px-4 max-w-7xl mx-auto space-y-6" x-data="{ selectedId: null, actionType: null }">

        {{-- Active Assignments --}}
        <div>
            <h3
                class="text-lg font-bold text-gray-700 dark:text-gray-300 mb-4 border-b border-gray-200 dark:border-gray-700 pb-2">
                Pendientes</h3>

            @if($activeAssignments->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($activeAssignments as $assignment)
                        <div
                            class="bg-white dark:bg-gray-800 shadow rounded-lg p-6 border-l-4 border-blue-500 flex flex-col justify-between transition-transform duration-200 hover:-translate-y-1 hover:shadow-lg">
                            <div>
                                <div class="flex items-center gap-3 mb-2">
                                    <h3 class="text-xl font-bold text-gray-900 dark:text-gray-100">
                                        {{ $assignment->territory->code }}
                                    </h3>
                                    <span
                                        class="text-xs font-semibold text-gray-500 bg-gray-100 dark:bg-gray-700 dark:text-gray-300 px-2 py-1 rounded-md">
                                        {{ $assignment->territory->city->name }} -
                                        {{ $assignment->territory->neighborhood_name }}
                                    </span>
                                </div>
                                <div class="flex items-center gap-3 mb-3">
                                    <img src="{{ $assignment->assignedTo->profile_photo_url }}"
                                        alt="{{ $assignment->assignedTo->name }}"
                                        class="h-10 w-10 rounded-full object-cover border border-gray-100 dark:border-gray-700 shadow-sm">
                                    <div class="text-sm text-gray-700 dark:text-gray-300">
                                        <div class="text-xs text-gray-500 dark:text-gray-400 font-medium uppercase">
                                            {{ __('Publicador') }}
                                        </div>
                                        <div class="font-bold">{{ $assignment->assignedTo->name }}</div>
                                    </div>
                                </div>
                                <div class="text-sm space-y-1">
                                    <div class="text-gray-600 dark:text-gray-400">
                                        <span class="font-medium">Asignado:</span>
                                        {{ $assignment->assigned_at->format('d/m/Y') }}
                                    </div>
                                    @if($assignment->due_date)
                                        @php
                                            $now = now()->startOfDay();
                                            $due = $assignment->due_date->copy()->startOfDay();
                                            // diffInDays con 'false' devuelve negativo si $due está en el pasado
                                            $daysLeft = $now->diffInDays($due, false);

                                            $dateColorClass = 'text-gray-500 dark:text-gray-400';
                                            $timeText = '';
                                            if ($daysLeft < 0) {
                                                $dateColorClass = 'text-red-600 dark:text-red-400 font-bold';
                                                $timeText = '(Vencido)';
                                            } elseif ($daysLeft <= 7) {
                                                $dateColorClass = 'text-amber-600 dark:text-amber-400 font-bold';
                                                $timeText = '(En ' . intval($daysLeft) . ' ' . (intval($daysLeft) === 1 ? 'día' : 'días') . ')';
                                            }
                                          @endphp
                                        <div class="{{ $dateColorClass }} text-sm flex items-center gap-1.5 mt-1">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            <span>
                                                Límite: {{ $assignment->due_date->format('d/m/Y') }}
                                                @if($timeText)
                                                    <span class="text-[11px] ml-1">{{ $timeText }}</span>
                                                @endif
                                            </span>
                                        </div>
                                    @endif
                                </div>
                            </div>
                            <div class="flex flex-col gap-2 mt-6">
                                <!-- Update Form -->
                                <form id="complete-assignment-{{ $assignment->id }}"
                                    action="{{ route('admin.personal-territories.update', $assignment) }}" method="POST"
                                    class="w-full">
                                    @csrf
                                    @method('PUT')
                                    <button type="button"
                                        @click="selectedId = {{ $assignment->id }}; actionType='complete'; $dispatch('open-modal', 'confirm-action')"
                                        class="w-full px-5 py-2 text-sm font-bold text-white bg-green-600 hover:bg-green-700 rounded-lg shadow-sm transition-colors text-center border border-green-700 flex items-center justify-center gap-2">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M5 13l4 4L19 7" />
                                        </svg>
                                        Marcar como completado
                                    </button>
                                </form>

                                <!-- Delete Form -->
                                <form id="delete-assignment-{{ $assignment->id }}"
                                    action="{{ route('admin.personal-territories.destroy', $assignment) }}" method="POST"
                                    class="w-full">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button"
                                        @click="selectedId = {{ $assignment->id }}; actionType='delete'; $dispatch('open-modal', 'confirm-action')"
                                        class="w-full px-5 py-2 text-sm font-bold text-red-600 bg-red-50 hover:bg-red-100 dark:text-red-400 dark:bg-red-900/20 dark:hover:bg-red-900/40 rounded-lg shadow-sm transition-colors text-center border border-red-200 dark:border-red-800 flex items-center justify-center gap-2">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                        Cancelar asignación
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div
                    class="bg-white dark:bg-gray-800 shadow rounded-lg py-12 px-6 text-center text-gray-500 dark:text-gray-400">
                    <p class="text-sm font-medium">No hay asignaciones personales pendientes.</p>
                </div>
            @endif
        </div>

        {{-- Completed Assignments --}}
        <div>
            <h3
                class="text-lg font-bold text-gray-700 dark:text-gray-300 mb-4 mt-8 border-b border-gray-200 dark:border-gray-700 pb-2">
                Historial Reciente</h3>
            <div class="bg-white dark:bg-gray-800 shadow rounded-lg overflow-hidden">
                @if($completedAssignments->count() > 0)
                    <ul class="divide-y divide-gray-200 dark:divide-gray-700 opacity-75">
                        @foreach($completedAssignments as $assignment)
                            <li class="p-6 transition-colors hover:bg-gray-50 dark:hover:bg-gray-700/50">
                                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center gap-3 mb-1">
                                            <h3 class="text-xl font-bold text-gray-900 dark:text-gray-100 truncate">
                                                {{ $assignment->territory->code }}
                                            </h3>
                                            <span
                                                class="text-xs font-semibold text-gray-500 bg-gray-100 dark:bg-gray-700 dark:text-gray-300 px-2.5 py-1 rounded-md whitespace-nowrap">
                                                {{ $assignment->territory->city->name }} -
                                                {{ $assignment->territory->neighborhood_name }}
                                            </span>
                                        </div>
                                        <div
                                            class="text-sm text-gray-700 dark:text-gray-300 mt-2 flex flex-col sm:flex-row sm:items-center gap-3">
                                            <div class="flex items-center gap-2">
                                                <img src="{{ $assignment->assignedTo->profile_photo_url }}"
                                                    alt="{{ $assignment->assignedTo->name }}"
                                                    class="h-6 w-6 rounded-full object-cover">
                                                <div class="truncate">
                                                    <span
                                                        class="font-medium text-gray-500 dark:text-gray-400">Publicador:</span>
                                                    <span class="font-semibold">{{ $assignment->assignedTo->name }}</span>
                                                </div>
                                            </div>
                                            <span class="hidden sm:inline-block text-gray-300 dark:text-gray-600">|</span>
                                            <div class="truncate">
                                                <span class="font-medium text-gray-500 dark:text-gray-400">Asignado:</span>
                                                {{ $assignment->assigned_at->format('d/m/Y') }}
                                            </div>
                                        </div>
                                    </div>

                                    <div class="mt-3 sm:mt-0 w-full sm:w-auto flex-shrink-0 flex items-center gap-2">
                                        <span
                                            class="inline-flex w-full sm:w-auto items-center justify-center px-4 py-2.5 text-sm font-semibold text-green-700 bg-green-50 border border-green-200 rounded-lg dark:text-green-400 dark:bg-green-900/30 dark:border-green-800/50 shadow-sm whitespace-nowrap">
                                            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M5 13l4 4L19 7" />
                                            </svg>
                                            Completado el {{ $assignment->completed_at->format('d/m/Y') }}
                                        </span>

                                        <!-- Admin Delete History Entry -->
                                        <form id="delete-assignment-{{ $assignment->id }}"
                                            action="{{ route('admin.personal-territories.destroy', $assignment) }}"
                                            method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button"
                                                @click="selectedId = {{ $assignment->id }}; actionType='delete'; $dispatch('open-modal', 'confirm-action')"
                                                class="p-2 text-gray-400 hover:text-red-600 transition-colors"
                                                title="Eliminar este registro">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                @else
                    <div class="py-8 px-6 text-center text-gray-500 dark:text-gray-400">
                        <p class="text-sm">No hay asignaciones personales completadas aún.</p>
                    </div>
                @endif
            </div>
        </div>

        {{-- Dynamic Confirmation Modal --}}
        <x-modal-confirm name="confirm-action" title="Confirmar Acción"
            content="¿Estás seguro de que deseas proceder con esta acción?">
            <x-slot name="footer">
                <x-secondary-button x-on:click="$dispatch('close')">
                    Cancelar
                </x-secondary-button>

                <x-danger-button class="ml-3" x-show="actionType === 'delete'"
                    x-on:click="document.getElementById('delete-assignment-' + selectedId).submit()">
                    Sí, Cancelar Asignación
                </x-danger-button>
                <button type="button"
                    class="ml-3 inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 focus:bg-green-700 active:bg-green-900 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition ease-in-out duration-150"
                    x-show="actionType === 'complete'"
                    x-on:click="document.getElementById('complete-assignment-' + selectedId).submit()">
                    Sí, Completar
                </button>
            </x-slot>
        </x-modal-confirm>

    </div>
</x-app-layout>