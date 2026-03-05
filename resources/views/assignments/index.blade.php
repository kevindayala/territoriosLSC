<x-app-layout>
    <x-slot name="title">Mis Asignaciones</x-slot>
    <x-slot name="logo_url">{{ route('dashboard') }}</x-slot>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Mis Asignaciones') }}
            </h2>
        </div>
    </x-slot>

    <div class="py-6 px-4 max-w-7xl mx-auto space-y-8" x-data="{ selectedAssignmentId: null }">

        {{-- Active Assignments --}}
        <div>
            <h3 class="text-lg font-bold text-gray-700 dark:text-gray-300 mb-4 border-b pb-2">En Curso</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @forelse($activeAssignments as $assignment)
                    <div
                        class="bg-white dark:bg-gray-800 shadow rounded-lg p-6 border-l-4 border-blue-500 flex flex-col justify-between transition-transform duration-200 hover:-translate-y-1 hover:shadow-lg">
                        <div>
                            <div class="flex items-center gap-2 mb-2">
                                <h3 class="text-xl font-bold text-gray-900 dark:text-gray-100">
                                    {{ $assignment->territory->code }}
                                </h3>
                                <span
                                    class="text-xs font-semibold text-gray-500 bg-gray-100 dark:bg-gray-700 dark:text-gray-300 px-2 py-1 rounded-md uppercase">
                                    {{ $assignment->territory->city->name ?? '' }} -
                                    {{ $assignment->territory->neighborhood_name }}
                                </span>
                            </div>

                            @if($assignment->type === 'personal')
                                <div class="mb-3">
                                    <span
                                        class="px-2 py-0.5 text-[10px] uppercase font-bold tracking-wider text-amber-700 bg-amber-100 rounded-full dark:bg-amber-500/30 dark:text-amber-200 border border-amber-200 dark:border-amber-400/60 shadow-sm">
                                        Personal
                                    </span>
                                </div>
                            @endif

                            <div class="text-sm space-y-1 mt-3">
                                <div class="text-gray-600 dark:text-gray-400">
                                    <span class="font-medium">Asignado:</span>
                                    {{ $assignment->assigned_at->format('d/m/Y') }}
                                </div>
                                @if($assignment->type === 'personal' && $assignment->due_date)
                                    <div class="text-amber-600 dark:text-amber-400 font-medium flex items-center gap-1">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        Límite: {{ $assignment->due_date->format('d/m/Y') }}
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div class="flex flex-col gap-2 mt-6">
                            <a href="{{ route('territories.show', $assignment->territory) }}"
                                class="w-full px-4 py-2.5 text-sm font-semibold text-white bg-blue-600 hover:bg-blue-700 rounded-lg shadow-sm transition-all duration-200 border border-blue-700 flex items-center justify-center gap-1.5 hover:shadow-md focus:outline-none focus:ring-2 focus:ring-blue-500/50">
                                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                                Ver Territorio
                            </a>

                            <form id="complete-assignment-{{ $assignment->id }}"
                                action="{{ route('assignments.update', $assignment) }}" method="POST" class="w-full">
                                @csrf
                                @method('PUT')
                                <button type="button"
                                    @click="selectedAssignmentId = {{ $assignment->id }}; $dispatch('open-modal', 'confirm-complete')"
                                    class="w-full px-4 py-2.5 text-sm font-semibold text-white bg-green-600 hover:bg-green-700 rounded-lg shadow-sm transition-all duration-200 border border-green-700 flex items-center justify-center gap-2 hover:shadow-md">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M5 13l4 4L19 7" />
                                    </svg>
                                    Marcar como completado
                                </button>
                            </form>

                            <form id="cancel-assignment-{{ $assignment->id }}"
                                action="{{ route('assignments.destroy', $assignment) }}" method="POST" class="w-full">
                                @csrf
                                @method('DELETE')
                                <button type="button"
                                    @click="selectedAssignmentId = {{ $assignment->id }}; $dispatch('open-modal', 'confirm-cancel')"
                                    class="w-full px-4 py-2.5 text-sm font-semibold text-red-600 bg-red-50 hover:bg-red-100 dark:bg-red-900/20 dark:text-red-400 dark:hover:bg-red-900/40 rounded-lg shadow-sm transition-all duration-200 border border-red-200 hover:border-red-300 dark:border-red-800 dark:hover:border-red-700 flex items-center justify-center gap-1.5 focus:outline-none focus:ring-2 focus:ring-red-500/50">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                    Cancelar asignación
                                </button>
                            </form>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full text-center py-6 bg-gray-50 dark:bg-gray-800 rounded-lg text-gray-500">
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
                        <li
                            class="px-6 py-4 flex justify-between items-center hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                            <div>
                                <span
                                    class="font-bold text-gray-900 dark:text-gray-100">{{ $assignment->territory->code }}</span>
                                <span
                                    class="text-sm text-gray-500 dark:text-gray-400 ml-2">{{ $assignment->territory->neighborhood_name }}</span>
                            </div>
                            <span class="text-sm text-green-600 dark:text-green-400 font-medium">
                                {{ $assignment->completed_at->format('d/m/Y') }}
                            </span>
                        </li>
                    @empty
                        <li class="px-6 py-4 text-center text-gray-500 text-sm">No hay historial reciente.</li>
                    @endforelse
                </ul>
            </div>
        </div>

        {{-- CTA section moved to bottom --}}
        <div
            class="bg-white dark:bg-gray-800 rounded-2xl p-6 border border-gray-100 dark:border-gray-700 shadow-sm text-left sm:text-center">
            <p class="text-gray-600 dark:text-gray-400 font-medium mb-4">
                Solicita un territorio para trabajar de forma personal
            </p>
            <a href="{{ route('territory-requests.create') }}"
                class="inline-flex items-center px-6 py-3 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-xl shadow-lg shadow-emerald-500/20 active:scale-95 transition-all gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 4v16m8-8H4" />
                </svg>
                Solicitar Territorio Personal
            </a>
        </div>

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

        {{-- Confirmation Modal for Completion --}}
        <x-modal-confirm name="confirm-complete" title="Completar Territorio"
            content="¿Confirmas que has completado la predicación de este territorio? Al marcarlo como completado, pasará a tu historial.">
            <x-slot name="footer">
                <x-secondary-button x-on:click="$dispatch('close')">
                    Aún no
                </x-secondary-button>

                <button type="button"
                    class="ml-3 inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 focus:bg-green-700 active:bg-green-900 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition ease-in-out duration-150"
                    x-on:click="document.getElementById('complete-assignment-' + selectedAssignmentId).submit()">
                    Sí, completado
                </button>
            </x-slot>
        </x-modal-confirm>

    </div>
</x-app-layout>