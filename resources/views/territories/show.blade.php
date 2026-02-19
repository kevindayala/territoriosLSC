<x-app-layout>
    <x-slot name="logo_url">{{ route('territories.index') }}</x-slot>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div class="flex flex-col">
                <span class="text-sm font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wide">
                    {{ $territory->code }} - {{ $territory->city->name }}
                </span>
                <h2 class="font-black text-3xl text-gray-900 dark:text-white leading-tight">
                    {{ $territory->neighborhood_name }}
                </h2>
            </div>
            <div class="flex items-center gap-2">

                @if($currentAssignment)
                    <span class="px-2 py-1 text-xs rounded-full bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300">
                        {{ __('Asignado') }}
                    </span>
                @else
                    <span
                        class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300">
                        {{ __('Disponible') }}
                    </span>
                @endif
            </div>
        </div>
    </x-slot>

    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8 space-y-6">

        {{-- Warning Section --}}
        @if($recentCompletionWarning)
            <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-yellow-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                            fill="currentColor">
                            <path fill-rule="evenodd"
                                d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"
                                clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-yellow-700">
                            Este territorio fue completado recientemente
                            ({{ $territory->last_completed_at->format('d/m/Y') }} -
                            @php
                                $diff = '';
                                if ($territory->last_completed_at->isToday()) {
                                    $diff = __('Hoy');
                                } elseif ($territory->last_completed_at->isYesterday()) {
                                    $diff = __('Ayer');
                                } else {
                                    $diff = $territory->last_completed_at->copy()->startOfDay()->diffForHumans(now()->startOfDay(), ['syntax' => 1, 'parts' => 1]);
                                }
                            @endphp
                            {{ $diff }}).
                        </p>
                    </div>
                </div>
            </div>
        @endif

        {{-- Actions Section --}}
        @if($currentAssignment || $territory->status !== 'active' || auth()->user()->hasAnyRole(['admin', 'capitan']))
            <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
                @if($currentAssignment)
                    <div class="text-center">
                        <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-3">
                            {{ __('Territorio asignado') }}
                        </h3>
                        <div class="space-y-1 mb-6">
                            <p class="text-base text-gray-700 dark:text-gray-300">
                                <span class="font-bold">{{ __('Responsable') }}:</span>
                                {{ $currentAssignment->assignedTo->name }}
                            </p>
                            <p class="text-sm text-gray-500">
                                <span class="font-medium">{{ __('Desde') }}:</span>
                                {{ $currentAssignment->assigned_at->format('d/m/Y') }}
                            </p>
                        </div>

                        @if($currentAssignment->assigned_to_user_id === auth()->id() || auth()->user()->hasRole('admin'))
                            <form action="{{ route('assignments.update', $currentAssignment) }}" method="POST">
                                @csrf
                                @method('PUT')
                                <button type="submit"
                                    class="w-full bg-green-600 text-white font-bold py-3 rounded-lg shadow-md hover:bg-green-700 transition">
                                    Marcar como completado
                                </button>
                            </form>
                        @endif
                    </div>
                @elseif($territory->status === 'active')
                    <form action="{{ route('assignments.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="territory_id" value="{{ $territory->id }}">
                        <button type="submit"
                            class="w-full bg-emerald-600 text-white font-bold py-3 rounded-lg shadow-md hover:bg-emerald-700 transition">
                            Tomar territorio
                        </button>
                    </form>
                @else
                    <p class="text-center text-red-500 font-bold">Territorio Inactivo</p>
                @endif
            </div>
        @endif

        {{-- Persons Section --}}
        <div class="space-y-4">
            <div class="flex items-center justify-between px-1">
                <h3 class="text-lg font-black text-gray-800 dark:text-gray-100 flex items-center gap-2">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                    Personas / Direcciones
                </h3>
                <span
                    class="bg-blue-100 text-blue-800 dark:bg-blue-900/50 dark:text-blue-300 py-1 px-3 rounded-full text-xs font-bold">
                    {{ $territory->persons->count() }} Personas
                </span>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @forelse($territory->persons as $person)
                    <div
                        class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm hover:shadow-md border border-gray-100 dark:border-gray-700 flex flex-col h-full transition-all duration-300 group relative overflow-hidden">

                        <!-- Decoration -->
                        <div
                            class="absolute top-0 right-0 w-24 h-24 bg-gradient-to-br from-blue-50 to-transparent dark:from-blue-900/20 rounded-bl-full -mr-6 -mt-6 opacity-60 pointer-events-none">
                        </div>

                        <div class="p-6 flex flex-col h-full relative z-10">
                            <!-- Header: Icon & Name -->
                            <div class="flex items-center gap-4 mb-4">
                                <div
                                    class="w-12 h-12 rounded-xl bg-blue-50 dark:bg-blue-900/20 text-blue-600 dark:text-blue-400 flex items-center justify-center flex-shrink-0">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                    </svg>
                                </div>
                                <h4 class="text-lg font-bold text-gray-900 dark:text-white leading-tight">
                                    {{ $person->full_name }}
                                </h4>
                            </div>

                            <!-- Address Section -->
                            <div class="mb-3 bg-gray-50 dark:bg-gray-700/30 rounded-xl p-3.5">
                                <div class="flex items-start gap-2.5">
                                    <svg class="w-5 h-5 text-gray-400 dark:text-gray-500 mt-0.5 flex-shrink-0" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                    <span class="text-sm font-bold text-gray-700 dark:text-gray-200 leading-snug">
                                        {{ $person->address }}
                                    </span>
                                </div>
                            </div>

                            <!-- Notes Section -->
                            @if($person->notes)
                                <div class="mb-3 bg-amber-50 dark:bg-amber-900/10 rounded-xl p-3.5">
                                    <div class="flex items-start gap-2.5">
                                        <svg class="w-4 h-4 text-amber-500 mt-0.5 flex-shrink-0" fill="none"
                                            stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                        <span class="text-sm text-amber-900 dark:text-amber-200 font-medium leading-snug">
                                            {{ $person->notes }}
                                        </span>
                                    </div>
                                </div>
                            @endif

                            <!-- Spacer to push footer to bottom -->
                            <div class="flex-grow"></div>

                            <!-- Action Footer -->
                            <div class="mt-4 pt-4 border-t border-gray-100 dark:border-gray-700 flex gap-3">
                                @if($person->map_url)
                                    <a href="{{ $person->map_url }}" target="_blank"
                                        class="flex items-center justify-center flex-1 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-xl shadow-sm shadow-emerald-200 dark:shadow-none transition-all hover:scale-[1.02] text-xs sm:text-sm">
                                        <svg class="w-4 h-4 mr-2 opacity-90" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7" />
                                        </svg>
                                        Ver mapa
                                    </a>
                                @endif

                                <a href="{{ route('persons.edit', $person) }}"
                                    class="flex items-center justify-center flex-1 py-2.5 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700 text-gray-600 dark:text-gray-300 font-semibold rounded-xl transition-all hover:border-gray-300 dark:hover:border-gray-500 text-xs sm:text-sm">
                                    <svg class="w-4 h-4 mr-2 text-gray-400 dark:text-gray-500" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                    </svg>
                                    Sugerir edición
                                </a>
                            </div>
                        </div>
                    </div>
                @empty
                    <div
                        class="col-span-1 md:col-span-2 lg:col-span-3 py-12 text-center bg-white dark:bg-gray-800 rounded-2xl border-2 border-dashed border-gray-200 dark:border-gray-700">
                        <div
                            class="bg-gray-50 dark:bg-gray-700/50 rounded-full w-16 h-16 flex items-center justify-center mx-auto mb-3">
                            <svg class="h-8 w-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                            </svg>
                        </div>
                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                            {{ __('No hay personas registradas') }}
                        </h3>
                        <p class="mt-1 text-sm text-gray-500">
                            {{ __('Este territorio está vacío por el momento.') }}
                        </p>
                    </div>
                @endforelse
            </div>
        </div>

        {{-- History Section --}}
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Historial</h3>
            </div>
            <ul class="divide-y divide-gray-200 dark:divide-gray-700">
                @foreach($territory->assignments->whereNotNull('completed_at')->sortByDesc('completed_at')->take(5) as $assignment)
                    <li class="px-6 py-4">
                        <div class="flex justify-between">
                            <span
                                class="font-medium text-gray-900 dark:text-gray-100">{{ $assignment->assignedTo->name }}</span>
                            <span class="text-gray-500 text-sm">Completado:
                                {{ $assignment->completed_at->format('d/m/Y') }}</span>
                        </div>
                        <div class="text-xs text-gray-400 mt-1">
                            Asignado: {{ $assignment->assigned_at->format('d/m/Y') }}
                        </div>
                    </li>
                @endforeach
            </ul>
        </div>
    </div>
</x-app-layout>