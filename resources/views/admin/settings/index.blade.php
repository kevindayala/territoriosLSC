<x-app-layout>
    <x-slot name="logo_url">{{ route('dashboard') }}</x-slot>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Ajustes de Territorio') }}
        </h2>
    </x-slot>

    <div class="py-6 sm:py-12" x-data>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">

                {{-- Ciudades --}}
                <a href="{{ route('cities.index') }}"
                    class="group block p-6 bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-2xl hover:shadow-lg transition-all duration-300 hover:-translate-y-1 hover:border-indigo-500 border border-gray-100 dark:border-gray-700">
                    <div
                        class="flex flex-col items-center justify-center space-y-4 text-gray-500 dark:text-gray-400 group-hover:text-indigo-600 dark:group-hover:text-indigo-400">
                        <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4">
                            </path>
                        </svg>
                        <h3 class="text-lg font-bold uppercase tracking-widest text-center">Ciudades</h3>
                        <p class="text-sm text-center text-gray-400 dark:text-gray-500 line-clamp-2">Gestionar ciudades
                            registradas
                        </p>
                    </div>
                </a>




                {{-- Territorios CRUD (The full list) --}}
                <a href="{{ route('admin.territories.index') }}"
                    class="group block p-6 bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-2xl hover:shadow-lg transition-all duration-300 hover:-translate-y-1 hover:border-indigo-500 border border-gray-100 dark:border-gray-700">
                    <div
                        class="flex flex-col items-center justify-center space-y-4 text-gray-500 dark:text-gray-400 group-hover:text-indigo-600 dark:group-hover:text-indigo-400">
                        <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z">
                            </path>
                        </svg>
                        <h3 class="text-lg font-bold uppercase tracking-widest text-center">Territorios</h3>
                        <p class="text-sm text-center text-gray-400 dark:text-gray-500 line-clamp-2">Crear y Editar
                            Territorios</p>
                    </div>
                </a>

                {{-- Sordos Inactivos --}}
                <a href="{{ route('admin.persons.inactive') }}"
                    class="group block p-6 bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-2xl hover:shadow-lg transition-all duration-300 hover:-translate-y-1 hover:border-indigo-500 border border-gray-100 dark:border-gray-700">
                    <div
                        class="flex flex-col items-center justify-center space-y-4 text-gray-500 dark:text-gray-400 group-hover:text-indigo-600 dark:group-hover:text-indigo-400">
                        <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636">
                            </path>
                        </svg>
                        <h3 class="text-lg font-bold uppercase tracking-widest text-center">Inactivos</h3>
                        <p class="text-sm text-center text-gray-400 dark:text-gray-500 line-clamp-2">Ver y reactivar
                            sordos inactivos</p>
                    </div>
                </a>

                {{-- Territorios Personales --}}
                <a href="{{ route('admin.personal-territories.index') }}"
                    class="group block p-6 bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-2xl hover:shadow-lg transition-all duration-300 hover:-translate-y-1 hover:border-indigo-500 border border-gray-100 dark:border-gray-700">
                    <div
                        class="flex flex-col items-center justify-center space-y-4 text-gray-500 dark:text-gray-400 group-hover:text-indigo-600 dark:group-hover:text-indigo-400">
                        <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 21v-2a4 4 0 00-4-4H9a4 4 0 00-4 4v2" />
                        </svg>
                        <h3 class="text-lg font-bold uppercase tracking-widest text-center">Territorios Personales</h3>
                        <p class="text-sm text-center text-gray-400 dark:text-gray-500 line-clamp-2">Gestionar
                            territorios personales</p>
                    </div>
                </a>

                {{-- Usuarios --}}
                <a href="{{ route('users.index') }}"
                    class="group block p-6 bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-2xl hover:shadow-lg transition-all duration-300 hover:-translate-y-1 hover:border-indigo-500 border border-gray-100 dark:border-gray-700">
                    <div
                        class="flex flex-col items-center justify-center space-y-4 text-gray-500 dark:text-gray-400 group-hover:text-indigo-600 dark:group-hover:text-indigo-400">
                        <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z">
                            </path>
                        </svg>
                        <h3 class="text-lg font-bold uppercase tracking-widest text-center">Usuarios</h3>
                        <p class="text-sm text-center text-gray-400 dark:text-gray-500 line-clamp-2">Gestionar usuarios
                            y rol</p>
                    </div>
                </a>

                {{-- Importar y Exportar --}}
                <button type="button" @click="$dispatch('open-modal', 'import-export-modal')"
                    class="group block p-6 bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-2xl hover:shadow-lg transition-all duration-300 hover:-translate-y-1 hover:border-amber-500 border border-gray-100 dark:border-gray-700 w-full">
                    <div
                        class="flex flex-col items-center justify-center space-y-4 text-gray-500 dark:text-gray-400 group-hover:text-amber-600 dark:group-hover:text-amber-400">
                        <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10" />
                        </svg>
                        <h3 class="text-lg font-bold uppercase tracking-widest text-center">Importar / Exportar</h3>
                        <p class="text-sm text-center text-gray-400 dark:text-gray-500 line-clamp-2">Sincroniza
                            territorios y sordos mediante Excel</p>
                    </div>
                </button>

                {{-- S-13 Registro de Asignaciones --}}
                <button type="button" @click="$dispatch('open-modal', 's13-export-modal')"
                    class="group block p-6 bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-2xl hover:shadow-lg transition-all duration-300 hover:-translate-y-1 hover:border-blue-500 border border-gray-100 dark:border-gray-700 w-full">
                    <div
                        class="flex flex-col items-center justify-center space-y-4 text-gray-500 dark:text-gray-400 group-hover:text-blue-600 dark:group-hover:text-blue-400">
                        <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                            </path>
                        </svg>
                        <h3 class="text-lg font-bold uppercase tracking-widest text-center">Registro S-13</h3>
                        <p class="text-sm text-center text-gray-400 dark:text-gray-500 line-clamp-2">Exportar registro
                            de asignaciones en PDF</p>
                    </div>
                </button>
                {{-- Registro Público Toggle --}}
                <div
                    class="group p-6 bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-2xl border border-gray-100 dark:border-gray-700 transition-all duration-300">
                    <div class="flex flex-col items-center justify-center space-y-4">
                        <div
                            class="p-4 rounded-2xl {{ $publicRegistration ? 'bg-green-50 text-green-600 dark:bg-green-900/20' : 'bg-red-50 text-red-600 dark:bg-red-900/20' }}">
                            <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                            </svg>
                        </div>
                        <div class="text-center">
                            <h3 class="text-lg font-bold uppercase tracking-widest text-gray-700 dark:text-gray-200">
                                Registro Público</h3>
                            <div class="mt-1 flex items-center justify-center gap-2">
                                <span
                                    class="w-2 h-2 rounded-full {{ $publicRegistration ? 'bg-green-500 animate-pulse' : 'bg-red-500' }}"></span>
                                <span class="text-[10px] font-black uppercase tracking-widest text-gray-400">
                                    {{ $publicRegistration ? 'Habilitado' : 'Deshabilitado' }}
                                </span>
                            </div>
                        </div>

                        <form action="{{ route('admin.settings.update') }}" method="POST" class="w-full pt-2">
                            @csrf
                            <input type="hidden" name="public_registration"
                                value="{{ $publicRegistration ? 'false' : 'true' }}">
                            <button type="submit"
                                class="w-full py-3 px-4 rounded-xl text-xs font-black uppercase tracking-widest transition-all {{ $publicRegistration ? 'bg-red-600 hover:bg-red-700 text-white shadow-lg shadow-red-200 dark:shadow-none' : 'bg-green-600 hover:bg-green-700 text-white shadow-lg shadow-green-200 dark:shadow-none' }}">
                                {{ $publicRegistration ? 'Deshabilitar' : 'Habilitar' }}
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para Importar/Exportar -->
    <x-modal name="import-export-modal" :show="$errors->has('file')" maxWidth="md">
        <form method="POST" action="{{ route('admin.territories.import') }}" enctype="multipart/form-data" class="p-6">
            @csrf
            <h2 class="text-lg font-bold text-gray-900 dark:text-gray-100 mb-4">
                Importar / Exportar Territorios
            </h2>

            <div class="mb-4 text-sm text-gray-600 dark:text-gray-400">
                <p class="mb-2"><strong>1.</strong> Descarga la plantilla vacía si vas a ingresar muchos datos nuevos a
                    la vez, o bien, <strong>Descarga la Copia de Seguridad</strong> si quieres tomar una foto actual de
                    todos tus registros para guardarla o modificarla masivamente.</p>
                <div class="mb-6 flex justify-center gap-3 flex-wrap">
                    <a href="{{ route('admin.territories.export-template') }}"
                        class="inline-flex items-center px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-bold rounded-xl transition-all shadow-sm gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                        </svg>
                        Descargar Plantilla Vacía
                    </a>

                    <a href="{{ route('admin.territories.export-backup') }}"
                        class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-bold rounded-xl transition-all shadow-sm gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4" />
                        </svg>
                        Exportar Base Actual (Backup)
                    </a>
                </div>

                <p class="mb-2 border-t border-gray-200 dark:border-gray-700 pt-4"><strong>2.</strong> Sube el archivo
                    Excel lleno con la información de los territorios y personas.</p>
            </div>

            <div class="mb-6" x-data="{ fileName: '' }">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2" for="file_upload_alt">
                    Archivo Excel a Importar (.xlsx, .csv)
                </label>
                <div class="flex items-center space-x-4">
                    <label
                        class="cursor-pointer inline-flex items-center px-4 py-2 bg-amber-50 dark:bg-amber-900/40 border border-transparent rounded-full font-semibold text-sm text-amber-700 dark:text-amber-400 hover:bg-amber-100 dark:hover:bg-amber-900/60 transition-all">
                        Elegir archivo
                        <input type="file" name="file" id="file_upload_alt" accept=".xlsx,.csv,.xls" required
                            class="hidden"
                            @change="fileName = $event.target.files[0] ? $event.target.files[0].name : ''">
                    </label>
                    <span class="text-sm text-gray-500 dark:text-gray-400"
                        x-text="fileName ? fileName : 'Ningún archivo seleccionado'"></span>
                </div>
                @error('file')
                    <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <div class="mt-6 flex justify-end gap-3">
                <x-secondary-button x-on:click="$dispatch('close')">
                    Cancelar
                </x-secondary-button>
                <x-primary-button>
                    Subir e Importar
                </x-primary-button>
            </div>
        </form>
    </x-modal>

    <!-- Modal para S-13 -->
    <x-modal name="s13-export-modal" maxWidth="md">
        <div class="p-6 sm:p-8">
            <h2 class="text-xl font-bold text-gray-900 dark:text-gray-100 mb-2 leading-tight">
                Exportar Registro S-13
            </h2>
            <p class="text-sm text-gray-500 dark:text-gray-400 mb-6 leading-relaxed">
                Seleccione el rango de fechas para generar el reporte de asignaciones de territorios.
            </p>

            <div class="space-y-5">
                <div>
                    <label for="s13_start_date"
                        class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Fecha de Inicio</label>
                    <div class="relative">
                        <input type="date" id="s13_start_date" name="start_date"
                            value="{{ \Carbon\Carbon::now()->subYear()->startOfMonth()->format('Y-m-d') }}"
                            class="block w-full px-4 py-3 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-xl text-gray-900 dark:text-white shadow-sm focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition-colors">
                    </div>
                </div>
                <div>
                    <label for="s13_end_date"
                        class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Fecha de Fin</label>
                    <div class="relative">
                        <input type="date" id="s13_end_date" name="end_date"
                            value="{{ \Carbon\Carbon::now()->endOfMonth()->format('Y-m-d') }}"
                            class="block w-full px-4 py-3 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-xl text-gray-900 dark:text-white shadow-sm focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition-colors">
                    </div>
                </div>
            </div>

            <div
                class="mt-8 pt-6 border-t border-gray-100 dark:border-gray-700 flex flex-col-reverse sm:flex-row justify-end gap-3">
                <button type="button" x-on:click="$dispatch('close')"
                    class="w-full sm:w-auto px-5 py-2.5 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-xl hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors text-center">
                    Cancelar
                </button>
                <button type="button" x-on:click="
                        const start = document.getElementById('s13_start_date').value;
                        const end = document.getElementById('s13_end_date').value;
                        if(start && end) {
                            window.open('{{ route('export.assignments') }}?start_date=' + start + '&end_date=' + end, '_blank'); 
                            $dispatch('close');
                        } else {
                            alert('Por favor complete ambas fechas.');
                        }
                   "
                    class="w-full sm:w-auto inline-flex items-center justify-center px-6 py-2.5 bg-blue-600 border border-transparent rounded-xl font-bold text-sm text-white hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 shadow-sm transition-all gap-2">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                        </path>
                    </svg>
                    Generar PDF
                </button>
            </div>
        </div>
    </x-modal>

    <!-- Flatpickr for forced Spanish Locale on Date Inputs -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://npmcdn.com/flatpickr/dist/l10n/es.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            flatpickr("#s13_start_date", {
                locale: "es",
                altInput: true,
                altFormat: "d/m/Y",
                dateFormat: "Y-m-d",
            });
            flatpickr("#s13_end_date", {
                locale: "es",
                altInput: true,
                altFormat: "d/m/Y",
                dateFormat: "Y-m-d",
            });
        });
    </script>
</x-app-layout>