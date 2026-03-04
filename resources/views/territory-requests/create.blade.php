<x-app-layout>
    <x-slot name="title">Solicitar Territorio</x-slot>

    <div class="py-12 flex items-center justify-center p-4">
        <div
            class="w-full max-w-md bg-white dark:bg-gray-800 rounded-2xl md:rounded-[2rem] shadow-xl border border-gray-100 dark:border-gray-700 overflow-hidden">

            <div class="p-6 md:p-8">
                <!-- Encabezado con título y botón de cerrar -->
                <div class="flex items-start justify-between mb-2">
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white leading-tight pr-4">
                        Solicitar Territorio Personal
                    </h2>
                    <a href="{{ route('dashboard') }}"
                        class="text-gray-400 hover:text-gray-600 dark:hover:text-white transition-colors p-1 -mt-1 -mr-2 flex-shrink-0"
                        title="Cancelar y volver">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </a>
                </div>
                <p class="text-sm text-gray-500 dark:text-gray-400 mb-8 leading-relaxed">
                    Selecciona un territorio y la fecha estimada en la que planeas devolverlo.
                </p>

                @if ($errors->any())
                    <div class="mb-6 bg-red-50 dark:bg-red-900/50 border-l-4 border-red-500 p-4 rounded-md">
                        <div class="flex items-start">
                            <div class="flex-shrink-0 mt-0.5">
                                <svg class="h-5 w-5 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                                        clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-sm font-medium text-red-800 dark:text-red-200">
                                    Hay {{ $errors->count() }} errores en tu solicitud:
                                </h3>
                                <ul class="mt-1 list-disc list-inside text-sm text-red-700 dark:text-red-300">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                @endif

                <form action="{{ route('territory-requests.store') }}" method="POST" class="space-y-6">
                    @csrf

                    <!-- Select Buscador (Alpine.js) -->
                    @php
                        // Preparar datos para Alpine.js
                        $territoriesData = $territories->map(function ($t) {
                            return [
                                'id' => $t->id,
                                'code' => $t->code,
                                'name' => $t->neighborhood_name,
                                'type' => $t->type,
                                'display' => '[' . $t->code . '] ' . $t->neighborhood_name . ($t->city ? ' - ' . $t->city->name : '') . ($t->type === 'business' ? ' (Negocios)' : '')
                            ];
                        })->values()->toJson();

                        $oldTerritoryId = old('territory_id');
                    @endphp

                    <div x-data="{
                            open: false,
                            search: '',
                            territories: {{ $territoriesData }},
                            selectedId: {{ $oldTerritoryId ? $oldTerritoryId : 'null' }},
                            get filteredTerritories() {
                                if (this.search === '') return []; // No mostrar nada si no hay búsqueda
                                return this.territories.filter(t => 
                                    t.code.toLowerCase().includes(this.search.toLowerCase()) || 
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

                        <input type="hidden" name="territory_id" :value="selectedId" required>

                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Territorio a solicitar <span class="text-red-500">*</span>
                        </label>

                        <button type="button" @click="open = !open; if(open) $nextTick(() => $refs.searchInput.focus())"
                            class="w-full text-left px-4 py-3 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-xl text-gray-700 dark:text-gray-300 focus:outline-none focus:ring-2 focus:ring-emerald-500 transition-all flex justify-between items-center group shadow-sm">
                            <span x-text="selectedDisplay"
                                :class="{'text-gray-900 dark:text-white font-medium': selectedId}"></span>
                            <svg class="w-5 h-5 text-gray-400 group-hover:text-gray-600 dark:group-hover:text-gray-300 transition-colors"
                                :class="{'rotate-180': open}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>

                        <!-- Dropdown Menu -->
                        <div x-show="open" x-transition:enter="transition ease-out duration-100"
                            x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                            x-transition:leave="transition ease-in duration-75"
                            x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
                            class="absolute z-50 w-full mt-2 bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700 rounded-xl shadow-lg ring-1 ring-black ring-opacity-5 overflow-hidden"
                            style="display: none;">

                            <!-- Buscador -->
                            <div
                                class="p-2 border-b border-gray-100 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/50">
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

                            <!-- Opciones -->
                            <ul class="max-h-60 overflow-y-auto p-1 custom-scrollbar">
                                <template x-for="territory in filteredTerritories" :key="territory.id">
                                    <li>
                                        <button type="button" @click="selectOption(territory.id)"
                                            class="w-full text-left px-3 py-2.5 rounded-lg text-sm flex items-center justify-between transition-colors"
                                            :class="{'bg-emerald-50 text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-400 font-medium': selectedId === territory.id, 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700': selectedId !== territory.id}">
                                            <span x-text="territory.display"></span>
                                            <svg x-show="selectedId === territory.id"
                                                class="w-4 h-4 text-emerald-600 dark:text-emerald-400 flex-shrink-0"
                                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M5 13l4 4L19 7"></path>
                                            </svg>
                                        </button>
                                    </li>
                                </template>

                                <!-- Mensaje Principal -->
                                <li x-show="search === ''"
                                    class="px-4 py-4 text-sm text-gray-500 dark:text-gray-400 text-center">
                                    Empieza a escribir para buscar un territorio...
                                </li>

                                <!-- No se encontró resultado -->
                                <li x-show="search !== '' && filteredTerritories.length === 0"
                                    class="px-4 py-4 text-sm text-gray-500 dark:text-gray-400 text-center">
                                    No se encontraron territorios.
                                </li>
                            </ul>
                        </div>
                    </div>

                    <style>
                        /* Custom scrollbar para el dropdown */
                        .custom-scrollbar::-webkit-scrollbar {
                            width: 6px;
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

                        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
                            background: #94A3B8;
                        }

                        .dark .custom-scrollbar::-webkit-scrollbar-thumb:hover {
                            background: #64748B;
                        }
                    </style>

                    <!-- Fecha estimada de devolución -->
                    <div>
                        <label for="expected_return_date"
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Fecha estimada para entregar <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <input type="date" id="expected_return_date" name="expected_return_date" required
                                value="{{ old('expected_return_date', date('Y-m-d')) }}" min="{{ date('Y-m-d') }}"
                                class="w-full px-4 py-3 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-xl text-gray-900 dark:text-white shadow-sm focus:outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 transition-colors">
                        </div>
                        <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">
                            Selecciona la fecha en la cual deseas entregar el territorio.
                        </p>
                    </div>

                    <!-- Botones -->
                    <div
                        class="pt-6 mt-6 border-t border-gray-100 dark:border-gray-700 flex flex-col sm:flex-row-reverse sm:items-center justify-between gap-3">
                        <button type="submit"
                            class="w-full sm:w-auto px-4 sm:px-6 py-3 text-sm font-bold text-white bg-emerald-600 border border-transparent rounded-xl hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 shadow-sm transition-all flex items-center justify-center gap-2">
                            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                            </svg>
                            <span class="sm:hidden">Enviar Solicitud</span>
                            <span class="hidden sm:inline">Confirmar y Enviar Solicitud</span>
                        </button>
                        <a href="{{ route('dashboard') }}"
                            class="w-full sm:w-auto text-center px-4 sm:px-5 py-3 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-xl hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 transition-colors">
                            Cancelar
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
    </div>

    <!-- Flatpickr for Date Input -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://npmcdn.com/flatpickr/dist/l10n/es.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            flatpickr("#expected_return_date", {
                locale: "es",
                altInput: true,
                altFormat: "d/m/Y",
                dateFormat: "Y-m-d",
                minDate: "today"
            });
        });
    </script>
</x-app-layout>