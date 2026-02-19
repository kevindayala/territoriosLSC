<x-app-layout>
    <x-slot name="logo_url">{{ route('dashboard') }}</x-slot>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Ajustes de Territorio') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">

                {{-- Ciudades --}}
                <a href="{{ route('cities.index') }}"
                    class="group block p-6 bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg hover:shadow-lg transition-all duration-300 hover:-translate-y-1 hover:border-indigo-500 border border-transparent">
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
                    class="group block p-6 bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg hover:shadow-lg transition-all duration-300 hover:-translate-y-1 hover:border-indigo-500 border border-transparent">
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
                    class="group block p-6 bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg hover:shadow-lg transition-all duration-300 hover:-translate-y-1 hover:border-indigo-500 border border-transparent">
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

                {{-- Usuarios --}}
                <a href="{{ route('users.index') }}"
                    class="group block p-6 bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg hover:shadow-lg transition-all duration-300 hover:-translate-y-1 hover:border-indigo-500 border border-transparent">
                    <div
                        class="flex flex-col items-center justify-center space-y-4 text-gray-500 dark:text-gray-400 group-hover:text-indigo-600 dark:group-hover:text-indigo-400">
                        <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z">
                            </path>
                        </svg>
                        <h3 class="text-lg font-bold uppercase tracking-widest text-center">Usuarios</h3>
                        <p class="text-sm text-center text-gray-400 dark:text-gray-500 line-clamp-2">Gestionar usuarios
                            y roles</p>
                    </div>
                </a>


            </div>
        </div>
    </div>
</x-app-layout>