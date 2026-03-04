<x-app-layout>
    <x-slot name="title">Inicio</x-slot>




    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-4 pb-8 md:pt-8 md:pb-12">

        {{-- Welcome Header --}}
        <div class="mb-12 md:hidden flex flex-col text-left">
            <h1 class="text-2xl md:text-4xl font-bold text-gray-900 dark:text-white">
                {{ __('Hola, :name', ['name' => strtok(auth()->user()->name, ' ')]) }} 👋
            </h1>

        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 md:gap-6">



            <a href="{{ route('territories.index') }}"
                class="group relative bg-white dark:bg-gray-800 rounded-2xl p-4 md:p-8 shadow-sm hover:shadow-xl hover:-translate-y-1 hover:scale-[1.02] border border-gray-100 dark:border-gray-700 hover:border-blue-300 dark:hover:border-blue-500 hover:bg-blue-50 dark:hover:bg-blue-900/40 transition-all duration-300 flex md:flex-col items-center gap-4 md:gap-6 active:scale-[0.98]">
                <div
                    class="flex-shrink-0 text-slate-400 dark:text-slate-500 group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors duration-300">
                    <svg class="w-6 h-6 md:w-8 md:h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                            d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z">
                        </path>
                    </svg>
                </div>
                <div class="flex-1 min-w-0 md:text-center">
                    <h3
                        class="text-base md:text-lg font-semibold text-slate-800 dark:text-slate-200 group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors">
                        {{ __('Territorios') }}
                    </h3>
                    <p
                        class="text-xs md:text-sm text-slate-400 dark:text-slate-500 mt-0.5 md:mt-2 truncate group-hover:text-slate-500">
                        Lista de territorios
                    </p>
                </div>
                <div class="md:hidden text-gray-300">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </div>
            </a>

            @role('admin')
            <a href="{{ route('approvals.index') }}"
                class="group relative bg-white dark:bg-gray-800 rounded-2xl p-4 md:p-8 shadow-sm hover:shadow-xl hover:-translate-y-1 hover:scale-[1.02] border border-gray-100 dark:border-gray-700 hover:border-blue-300 dark:hover:border-blue-500 hover:bg-blue-50 dark:hover:bg-blue-900/40 transition-all duration-300 flex md:flex-col items-center gap-4 md:gap-6 active:scale-[0.98]">
                <div
                    class="flex-shrink-0 text-slate-400 dark:text-slate-500 group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors duration-300">
                    <svg class="w-6 h-6 md:w-8 md:h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div class="flex-1 min-w-0 md:text-center">
                    <h3
                        class="text-base md:text-lg font-semibold text-slate-800 dark:text-slate-200 group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors">
                        {{ __('Aprobaciones') }}
                    </h3>
                    <p
                        class="text-xs md:text-sm text-slate-400 dark:text-slate-500 mt-0.5 md:mt-2 truncate group-hover:text-slate-500">
                        Revisar pendientes
                    </p>
                </div>
                <div class="md:hidden text-gray-300">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </div>
            </a>
            @endrole

            <a href="{{ route('persons.index') }}"
                class="group relative bg-white dark:bg-gray-800 rounded-2xl p-4 md:p-8 shadow-sm hover:shadow-xl hover:-translate-y-1 hover:scale-[1.02] border border-gray-100 dark:border-gray-700 hover:border-blue-300 dark:hover:border-blue-500 hover:bg-blue-50 dark:hover:bg-blue-900/40 transition-all duration-300 flex md:flex-col items-center gap-4 md:gap-6 active:scale-[0.98]">
                <div
                    class="flex-shrink-0 text-slate-400 dark:text-slate-500 group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors duration-300">
                    <svg class="w-6 h-6 md:w-8 md:h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                            d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z">
                        </path>
                    </svg>
                </div>
                <div class="flex-1 min-w-0 md:text-center">
                    <h3
                        class="text-base md:text-lg font-semibold text-slate-800 dark:text-slate-200 group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors">
                        {{ __('Sordos') }}
                    </h3>
                    <p
                        class="text-xs md:text-sm text-slate-400 dark:text-slate-500 mt-0.5 md:mt-2 truncate group-hover:text-slate-500">
                        Lista de personas
                    </p>
                </div>
                <div class="md:hidden text-gray-300">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </div>
            </a>

            <a href="{{ route('assignments.index') }}"
                class="group relative bg-white dark:bg-gray-800 rounded-2xl p-4 md:p-8 shadow-sm hover:shadow-xl hover:-translate-y-1 hover:scale-[1.02] border border-gray-100 dark:border-gray-700 hover:border-blue-300 dark:hover:border-blue-500 hover:bg-blue-50 dark:hover:bg-blue-900/40 transition-all duration-300 flex md:flex-col items-center gap-4 md:gap-6 active:scale-[0.98]">
                <div
                    class="flex-shrink-0 text-slate-400 dark:text-slate-500 group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors duration-300">
                    <svg class="w-6 h-6 md:w-8 md:h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                            d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01">
                        </path>
                    </svg>
                </div>
                <div class="flex-1 min-w-0 md:text-center">
                    <h3
                        class="text-base md:text-lg font-semibold text-slate-800 dark:text-slate-200 group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors">
                        {{ __('Asignaciones') }}
                    </h3>
                    <p
                        class="text-xs md:text-sm text-slate-400 dark:text-slate-500 mt-0.5 md:mt-2 truncate group-hover:text-slate-500">
                        Mis registros
                    </p>
                </div>
                <div class="md:hidden text-gray-300">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </div>
            </a>
            <a href="{{ route('territory-requests.create') }}"
                class="group relative bg-white dark:bg-gray-800 rounded-2xl p-4 md:p-8 shadow-sm hover:shadow-xl hover:-translate-y-1 hover:scale-[1.02] border border-gray-100 dark:border-gray-700 hover:border-emerald-300 dark:hover:border-emerald-500 hover:bg-emerald-50 dark:hover:bg-emerald-900/40 transition-all duration-300 flex md:flex-col items-center gap-4 md:gap-6 active:scale-[0.98]">
                <div
                    class="flex-shrink-0 text-slate-400 dark:text-slate-500 group-hover:text-emerald-600 dark:group-hover:text-emerald-400 transition-colors duration-300">
                    <svg class="w-6 h-6 md:w-8 md:h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                            d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V15a2 2 0 01-2 2h-2M8 7H6a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2v-2">
                        </path>
                    </svg>
                </div>
                <div class="flex-1 min-w-0 md:text-center">
                    <h3
                        class="text-base md:text-lg font-semibold text-slate-800 dark:text-slate-200 group-hover:text-emerald-600 dark:group-hover:text-emerald-400 transition-colors">
                        {{ __('Solicitar Territorio') }}
                    </h3>
                    <p
                        class="text-xs md:text-sm text-slate-400 dark:text-slate-500 mt-0.5 md:mt-2 truncate group-hover:text-slate-500">
                        Territorio personal
                    </p>
                </div>
                <div class="md:hidden text-gray-300">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </div>
            </a>

            <a href="{{ route('profile.edit') }}"
                class="group relative bg-white dark:bg-gray-800 rounded-2xl p-4 md:p-8 shadow-sm hover:shadow-xl hover:-translate-y-1 hover:scale-[1.02] border border-gray-100 dark:border-gray-700 hover:border-blue-300 dark:hover:border-blue-500 hover:bg-blue-50 dark:hover:bg-blue-900/40 transition-all duration-300 flex md:flex-col items-center gap-4 md:gap-6 active:scale-[0.98]">
                <div
                    class="flex-shrink-0 text-slate-400 dark:text-slate-500 group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors duration-300">
                    <svg class="w-6 h-6 md:w-8 md:h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                            d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                </div>
                <div class="flex-1 min-w-0 md:text-center">
                    <h3
                        class="text-base md:text-lg font-semibold text-slate-800 dark:text-slate-200 group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors">
                        {{ __('Mi Perfil') }}
                    </h3>
                    <p
                        class="text-xs md:text-sm text-slate-400 dark:text-slate-500 mt-0.5 md:mt-2 truncate group-hover:text-slate-500">
                        Cuenta y seguridad
                    </p>
                </div>
                <div class="md:hidden text-gray-300">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </div>
            </a>

            @role('admin')
            <a href="{{ route('admin.settings') }}"
                class="group relative bg-white dark:bg-gray-800 rounded-2xl p-4 md:p-8 shadow-sm hover:shadow-xl hover:-translate-y-1 hover:scale-[1.02] border border-gray-100 dark:border-gray-700 hover:border-blue-300 dark:hover:border-blue-500 hover:bg-blue-50 dark:hover:bg-blue-900/40 transition-all duration-300 flex md:flex-col items-center gap-4 md:gap-6 active:scale-[0.98]">
                <div
                    class="flex-shrink-0 text-slate-400 dark:text-slate-500 group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors duration-300">
                    <svg class="w-6 h-6 md:w-8 md:h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                            d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z">
                        </path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                </div>
                <div class="flex-1 min-w-0 md:text-center">
                    <h3
                        class="text-base md:text-lg font-semibold text-slate-800 dark:text-slate-200 group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors">
                        {{ __('Ajustes') }}
                    </h3>
                    <p
                        class="text-xs md:text-sm text-slate-400 dark:text-slate-500 mt-0.5 md:mt-2 truncate group-hover:text-slate-500">
                        Configuración global
                    </p>
                </div>
                <div class="md:hidden text-gray-300">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </div>
            </a>
            @endrole

        </div>
    </div>
</x-app-layout>