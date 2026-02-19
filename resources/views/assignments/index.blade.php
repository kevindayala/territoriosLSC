<x-app-layout>
    <x-slot name="logo_url">{{ route('dashboard') }}</x-slot>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Mis Asignaciones') }}
        </h2>
    </x-slot>

    <div class="py-6 px-4 max-w-7xl mx-auto space-y-8" x-data="{ selectedAssignmentId: null }">

        {{-- Active Assignments --}}
        <div>
            <h3 class="text-lg font-bold text-gray-700 dark:text-gray-300 mb-4 border-b pb-2">En Curso</h3>
            <div class="space-y-4">
                @forelse($activeAssignments as $assignment)
                    <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-5 border-l-4 border-blue-500">
                        <div class="flex justify-between items-start">
                            <div>
                                <span
                                    class="text-xs font-bold text-gray-500 uppercase">{{ $assignment->territory->neighborhood_name }}</span>
                                <h3 class="text-3xl font-bold text-gray-900 dark:text-gray-100">
                                    {{ $assignment->territory->code }}
                                </h3>
                                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                                    Asignado: {{ $assignment->assigned_at->format('d/m/Y') }}
                                </p>
                            </div>
                            <div class="flex gap-2">
                                <form id="cancel-assignment-{{ $assignment->id }}"
                                    action="{{ route('assignments.destroy', $assignment) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button"
                                        @click="selectedAssignmentId = {{ $assignment->id }}; $dispatch('open-modal', 'confirm-cancel')"
                                        class="bg-red-100 text-red-600 px-4 py-2 rounded-md font-bold shadow hover:bg-red-200 text-sm transition-colors">
                                        CANCELAR
                                    </button>
                                </form>
                                <a href="{{ route('territories.show', $assignment->territory) }}"
                                    class="bg-blue-600 text-white px-4 py-2 rounded-md font-bold shadow hover:bg-blue-700 text-sm transition-colors">
                                    IR
                                </a>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-6 bg-gray-50 dark:bg-gray-800 rounded-lg text-gray-500">
                        No tienes territorios asignados actualmente.
                    </div>
                @endforelse
            </div>
        </div>

        {{-- History --}}
        <div>
            <h3 class="text-lg font-bold text-gray-700 dark:text-gray-300 mb-4 border-b pb-2">Historial Reciente</h3>
            <div class="bg-white dark:bg-gray-800 shadow rounded-lg overflow-hidden">
                <ul class="divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($historyAssignments as $assignment)
                        <li class="px-6 py-4 flex justify-between items-center">
                            <div>
                                <span
                                    class="font-bold text-gray-900 dark:text-gray-100">{{ $assignment->territory->code }}</span>
                                <span
                                    class="text-sm text-gray-500 ml-2">{{ $assignment->territory->neighborhood_name }}</span>
                            </div>
                            <span class="text-sm text-green-600 font-medium">
                                {{ $assignment->completed_at->format('d/m/Y') }}
                            </span>
                        </li>
                    @empty
                        <li class="px-6 py-4 text-center text-gray-500 text-sm">No hay historial reciente.</li>
                    @endforelse
                </ul>
            </div>
        </div>

        {{-- Confirmation Modal --}}
        <x-modal-confirm name="confirm-cancel" title="Cancelar Asignación"
            content="¿Estás seguro de que deseas cancelar esta asignación? Esta acción no se puede deshacer.">
            <x-slot name="footer">
                <x-secondary-button x-on:click="$dispatch('close')">
                    No, mantener
                </x-secondary-button>

                <x-danger-button class="ml-3"
                    x-on:click="document.getElementById('cancel-assignment-' + selectedAssignmentId).submit()">
                    Sí, cancelar
                </x-danger-button>
            </x-slot>
        </x-modal-confirm>

    </div>
</x-app-layout>