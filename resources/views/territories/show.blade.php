<x-app-layout>
    <x-slot name="title">{{ $territory->neighborhood_name }}</x-slot>
    <x-slot name="logo_url">{{ route('territories.index') }}</x-slot>
    <x-slot name="header">
        <div class="flex justify-between items-start gap-4">
            <div class="flex flex-col flex-1 min-w-0">
                <span class="text-xs sm:text-sm font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wide">
                    {{ $territory->code }} - {{ $territory->city->name }}
                </span>
                <h2
                    class="font-black text-2xl sm:text-3xl text-gray-900 dark:text-white leading-tight mt-1 break-words">
                    {{ $territory->neighborhood_name }}
                </h2>
            </div>
            <div class="flex items-center gap-2 flex-shrink-0 mt-2 sm:mt-1">

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

        {{-- Actions Section --}}
        @if($currentAssignment || $territory->status !== 'active' || auth()->user()->hasAnyRole(['admin', 'capitan', 'publicador']))
            <div class="bg-white dark:bg-gray-800 rounded-3xl sm:rounded-[2rem] p-5 sm:p-7 shadow-sm border border-gray-100 dark:border-gray-700"
                x-data="{}">
                @if($currentAssignment)
                    <div class="text-center max-w-lg mx-auto">
                        <h3 class="text-lg font-black text-gray-900 dark:text-white mb-2">
                            {{ __('Territorio asignado') }}
                        </h3>
                        <div class="space-y-1 mb-6">
                            <p class="text-[13px] text-gray-800 dark:text-gray-200 font-medium">
                                <span class="font-bold text-gray-600 dark:text-gray-400">{{ __('Responsable') }}:</span>
                                {{ $currentAssignment->assignedTo->name }}
                            </p>
                            <p class="text-[13px] text-gray-500 dark:text-gray-400 font-medium">
                                {{ __('Desde') }}: {{ $currentAssignment->assigned_at->format('d/m/Y') }}
                            </p>
                        </div>

                        @if($currentAssignment->assigned_to_user_id === auth()->id() || auth()->user()->hasRole('admin'))
                            <div class="flex flex-col gap-3">
                                <form action="{{ route('assignments.update', $currentAssignment) }}" method="POST">
                                    @csrf
                                    @method('PUT')
                                    <button type="submit"
                                        class="w-full bg-[#1aa355] text-white font-bold text-[13px] py-2.5 rounded-xl hover:bg-[#168a47] transition-all flex items-center justify-center gap-2">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                                d="M5 13l4 4L19 7" />
                                        </svg>
                                        Marcar como completado
                                    </button>
                                </form>

                                <form action="{{ route('assignments.destroy', $currentAssignment) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                        class="w-full bg-[#faebeb] text-[#d63a3a] dark:bg-red-900/10 dark:text-red-400 font-bold text-[13px] py-2.5 rounded-xl border border-[#fad4d4] hover:bg-red-100 dark:hover:bg-red-900/20 transition-all flex items-center justify-center gap-2">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                                d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                        Cancelar asignación
                                    </button>
                                </form>
                            </div>
                        @endif
                    </div>
                @elseif($territory->status === 'active')
                    <div class="max-w-lg mx-auto">
                        <form action="{{ route('assignments.store') }}" method="POST" x-ref="takeForm">
                            @csrf
                            <input type="hidden" name="territory_id" value="{{ $territory->id }}">
                            <button type="button" @if($recentCompletionWarning)
                            @click.prevent="$dispatch('open-modal', 'confirm-recent-territory')" @else
                                @click.prevent="$refs.takeForm.submit()" @endif
                                class="w-full bg-[#1aa355] text-white font-bold py-3.5 rounded-xl hover:bg-[#168a47] transition-all flex items-center justify-center gap-2">
                                <svg class="w-5 h-5 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4">
                                    </path>
                                </svg>
                                {{ __('Tomar territorio') }}
                            </button>
                        </form>
                    </div>

                    @if($recentCompletionWarning)
                        <x-modal-confirm name="confirm-recent-territory" maxWidth="md">
                            <x-slot name="title">
                                <div class="flex items-center gap-2 text-amber-600">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z">
                                        </path>
                                    </svg>
                                    {{ __('Alerta') }}
                                </div>
                            </x-slot>

                            <x-slot name="content">
                                <p class="text-base text-gray-700 dark:text-gray-300">
                                    {{ __('Este territorio fue completado recientemente') }}
                                    <span class="font-bold underline">({{ $territory->last_completed_at->format('d/m/Y') }})</span>.
                                </p>
                                <p class="mt-4 text-sm text-gray-500">
                                    {{ __('¿Estás seguro de que quieres volver a asignarlo ahora?') }}
                                </p>
                            </x-slot>

                            <x-slot name="footer">
                                <x-secondary-button x-on:click="$dispatch('close')">
                                    {{ __('Cancelar') }}
                                </x-secondary-button>

                                <x-primary-button class="ml-3 bg-emerald-600 hover:bg-emerald-700"
                                    x-on:click="$refs.takeForm.submit()">
                                    {{ __('Sí, tomar territorio') }}
                                </x-primary-button>
                            </x-slot>
                        </x-modal-confirm>
                    @endif
                @else
                    <p class="text-center text-red-500 font-bold uppercase tracking-widest">{{ __('Territorio Inactivo') }}</p>
                @endif
            </div>
        @endif

        {{-- Persons Section --}}
        <div class="bg-white dark:bg-gray-800 rounded-3xl sm:rounded-[2rem] p-5 sm:p-7 shadow-sm border border-gray-100 dark:border-gray-700 space-y-6 mb-8"
            x-data="{ editOrderMode: false }">
            <div
                class="flex items-center justify-between flex-wrap gap-4 border-b border-gray-100 dark:border-gray-700 pb-4">
                <div class="flex items-center gap-3">
                    <h3 class="text-lg font-black text-gray-800 dark:text-gray-100 flex items-center gap-2">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                        Personas
                    </h3>
                    <span
                        class="bg-blue-100 text-blue-800 dark:bg-blue-900/50 dark:text-blue-300 py-1 px-3 rounded-full text-xs font-bold">
                        {{ $territory->persons->count() }}
                    </span>
                </div>
                @hasanyrole('admin|capitan')
                <div x-show="!editOrderMode" class="flex items-center gap-2">
                    <a href="{{ route('persons.create', ['territory_id' => $territory->id, 'redirect_to' => url()->current()]) }}"
                        class="px-4 py-2 text-sm font-bold rounded-lg transition-colors bg-blue-600 text-white hover:bg-blue-700 shadow-md shadow-blue-100 dark:shadow-none flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                d="M12 4v16m8-8H4" />
                        </svg>
                        Registrar
                    </a>
                    <button type="button" @click="editOrderMode = true"
                        class="px-4 py-2 text-sm font-semibold rounded-lg transition-colors border focus:outline-none focus:ring-2 focus:ring-offset-2 bg-white text-gray-700 border-gray-300 hover:bg-gray-50 dark:bg-gray-800 dark:text-gray-200 dark:border-gray-600 dark:hover:bg-gray-700">
                        <span class="flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 6h16M4 12h16M4 18h16"></path>
                            </svg>
                            Editar Orden
                        </span>
                    </button>
                </div>
                <div x-show="editOrderMode" style="display: none;" class="flex items-center gap-2">
                    <button type="button" @click="window.location.reload()"
                        class="px-4 py-2 text-sm font-semibold rounded-lg transition-colors border focus:outline-none focus:ring-2 focus:ring-offset-2 bg-white text-gray-700 border-gray-300 hover:bg-gray-50 dark:bg-gray-800 dark:text-gray-200 dark:border-gray-600 dark:hover:bg-gray-700">
                        Cancelar
                    </button>
                    <button type="button" @click="saveOrder()"
                        class="px-4 py-2 text-sm font-semibold rounded-lg transition-colors border focus:outline-none focus:ring-2 focus:ring-offset-2 bg-emerald-600 text-white border-emerald-600 hover:bg-emerald-700">
                        <span class="flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M5 13l4 4L19 7"></path>
                            </svg>
                            Guardar cambios
                        </span>
                    </button>
                </div>
                @endhasanyrole
            </div>

            <div class="flex flex-col gap-3" id="persons-grid">
                @forelse($territory->persons as $person)
                    <div data-id="{{ $person->id }}"
                        class="person-card bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl p-3 sm:p-4 sm:px-5 shadow-sm hover:shadow-md transition-all flex flex-col sm:flex-row sm:items-center gap-3 sm:gap-4 relative group">

                        {{-- Left Section: Avatar, Name, Address, Notes --}}
                        <div class="flex items-center gap-4 sm:gap-5 flex-1 min-w-0">
                            <div
                                class="w-11 h-11 rounded-full bg-blue-100 text-blue-700 dark:bg-blue-900/40 dark:text-blue-400 flex items-center justify-center flex-shrink-0 shadow-sm border border-blue-200 dark:border-blue-800">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                            </div>
                            <div class="flex flex-col min-w-0 gap-1">
                                <h4 class="text-[15px] sm:text-base font-bold text-gray-900 dark:text-white truncate">
                                    {{ $person->full_name }}
                                </h4>
                                <div class="flex items-center gap-1.5 mt-0.5">
                                    <svg class="w-4 h-4 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z">
                                        </path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    </svg>
                                    <span
                                        class="text-[13px] sm:text-sm font-semibold text-gray-500 dark:text-gray-400 truncate">
                                        {{ $person->address }}
                                    </span>
                                </div>
                                {{-- Notes (Now below address) --}}
                                @if($person->notes)
                                    <div class="flex items-start mt-0.5">
                                        <div class="inline-block text-[11px] bg-amber-50 dark:bg-amber-900/10 text-amber-800 dark:text-amber-300 px-2.5 py-1 rounded-lg font-medium border border-amber-100 dark:border-amber-900/30 truncate max-w-full"
                                            title="{{ $person->notes }}">
                                            📝 {{ $person->notes }}
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>

                        {{-- Right Section: Actions --}}
                        <div
                            class="flex items-center gap-2 justify-end sm:flex-shrink-0 mt-2 sm:mt-0 pt-3 sm:pt-0 border-t border-gray-100 dark:border-gray-700 sm:border-t-0">
                            @if($person->map_url)
                                <a href="{{ $person->map_url }}" target="_blank"
                                    class="flex items-center gap-1.5 p-2 px-3 sm:px-4 text-emerald-600 bg-emerald-50 hover:bg-emerald-100 dark:bg-emerald-900/20 dark:hover:bg-emerald-900/40 dark:text-emerald-400 rounded-xl font-black text-[10px] transition-all border border-emerald-100 dark:border-emerald-800/50"
                                    title="Ver mapa">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z">
                                        </path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    </svg>
                                    <span class="hidden sm:inline">VER MAPA</span>
                                    <span class="sm:hidden">MAPA</span>
                                </a>
                            @endif

                            <a href="{{ route('persons.edit', ['person' => $person, 'redirect_to' => url()->current()]) }}"
                                class="flex items-center gap-1.5 p-2 px-3 sm:px-4 text-blue-600 bg-blue-50 hover:bg-blue-100 dark:bg-blue-900/20 dark:hover:bg-blue-900/40 dark:text-blue-400 rounded-xl font-black text-[10px] transition-all border border-blue-100 dark:border-blue-800/50">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z">
                                    </path>
                                </svg>
                                <span class="hidden sm:inline">SUGERIR EDICIÓN</span>
                                <span class="sm:hidden">EDITAR</span>
                            </a>

                            @hasanyrole('admin|capitan')
                            <div x-show="editOrderMode" style="display: none;"
                                class="flex items-center gap-0.5 shrink-0 bg-gray-50 dark:bg-gray-700/50 rounded-xl border border-gray-200 dark:border-gray-600 p-0.5 shadow-sm ml-1">
                                <button type="button" onclick="moveUp(this)"
                                    class="p-1.5 text-gray-500 hover:text-blue-600 dark:text-gray-400 dark:hover:text-blue-400 rounded-lg hover:bg-white dark:hover:bg-gray-800 transition-colors active:scale-95 focus:outline-none shadow-sm"
                                    title="Mover arriba">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M5 15l7-7 7 7"></path>
                                    </svg>
                                </button>
                                <div class="w-px h-4 bg-gray-300 dark:bg-gray-600"></div>
                                <button type="button" onclick="moveDown(this)"
                                    class="p-1.5 text-gray-500 hover:text-blue-600 dark:text-gray-400 dark:hover:text-blue-400 rounded-lg hover:bg-white dark:hover:bg-gray-800 transition-colors active:scale-95 focus:outline-none shadow-sm"
                                    title="Mover abajo">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 9l-7 7-7-7"></path>
                                    </svg>
                                </button>
                            </div>
                            @endhasanyrole
                        </div>
                    </div>
                @empty
                    <div
                        class="py-16 text-center rounded-3xl sm:rounded-[2rem] border-2 border-dashed border-gray-200 dark:border-gray-700">
                        <div
                            class="bg-gray-50 dark:bg-gray-700/50 rounded-full w-16 h-16 flex items-center justify-center mx-auto mb-4">
                            <svg class="h-8 w-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">
                            {{ __('No hay personas registradas') }}
                        </h3>
                        <p class="text-sm text-gray-500 font-medium">
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

    @hasanyrole('admin|capitan')
    <script>
        function moveUp(btn) {
            let card = btn.closest('.person-card');
            let prev = card.previousElementSibling;
            if (prev) {
                // Remove visual focus to avoid glitchy appearance after move
                btn.blur();

                // Add an animation class
                card.style.transform = 'translateY(-10px)';
                card.style.transition = 'transform 0.2s';

                setTimeout(() => {
                    card.parentNode.insertBefore(card, prev);
                    card.style.transform = '';

                    // Small highlight to indicate it moved
                    card.classList.add('ring-2', 'ring-blue-400', 'dark:ring-blue-500');
                    setTimeout(() => card.classList.remove('ring-2', 'ring-blue-400', 'dark:ring-blue-500'), 500);
                }, 150);
            }
        }

        function moveDown(btn) {
            let card = btn.closest('.person-card');
            let next = card.nextElementSibling;
            if (next) {
                btn.blur();

                card.style.transform = 'translateY(10px)';
                card.style.transition = 'transform 0.2s';

                setTimeout(() => {
                    card.parentNode.insertBefore(next, card);
                    card.style.transform = '';

                    card.classList.add('ring-2', 'ring-blue-400', 'dark:ring-blue-500');
                    setTimeout(() => card.classList.remove('ring-2', 'ring-blue-400', 'dark:ring-blue-500'), 500);
                }, 150);
            }
        }

        function saveOrder() {
            let order = [];
            document.querySelectorAll('#persons-grid .person-card').forEach(function (card) {
                order.push(card.getAttribute('data-id'));
            });

            fetch('{{ route('territories.persons.reorder', $territory) }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ order: order })
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        window.location.reload();
                    }
                });
        }
    </script>
    @endhasanyrole
</x-app-layout>