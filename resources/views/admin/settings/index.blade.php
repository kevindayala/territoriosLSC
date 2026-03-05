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
            </div>
        </div>
    </div>
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


</x-app-layout>