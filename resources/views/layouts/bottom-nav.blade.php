<nav
    class="fixed bottom-0 w-full bg-white dark:bg-gray-800 border-t border-gray-200 dark:border-gray-600 flex justify-around items-center py-3 pb-safe z-50 md:hidden shadow-[0_-4px_6px_-1px_rgba(0,0,0,0.1)]">

    {{-- Territorios --}}
    <a href="{{ route('territories.index') }}"
        class="flex flex-col items-center justify-center w-full h-full py-1 {{ request()->routeIs('territories.*') ? 'text-blue-600 dark:text-blue-400' : 'text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200' }}">
        <svg class="w-7 h-7 mb-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"
            xmlns="http://www.w3.org/2000/svg">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z">
            </path>
        </svg>
        <span class="text-[10px] uppercase font-bold tracking-wide">Territorios</span>
    </a>

    {{-- Personas --}}
    <a href="{{ route('persons.index') }}"
        class="flex flex-col items-center justify-center w-full h-full py-1 {{ request()->routeIs('persons.*') ? 'text-blue-600 dark:text-blue-400' : 'text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200' }}">
        <svg class="w-7 h-7 mb-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"
            xmlns="http://www.w3.org/2000/svg">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z">
            </path>
        </svg>
        <span class="text-[10px] uppercase font-bold tracking-wide">Sordos</span>
    </a>

    {{-- Asignaciones --}}
    <a href="{{ route('assignments.index') }}"
        class="flex flex-col items-center justify-center w-full h-full py-1 {{ request()->routeIs('assignments.*') ? 'text-blue-600 dark:text-blue-400' : 'text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200' }}">
        <svg class="w-7 h-7 mb-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"
            xmlns="http://www.w3.org/2000/svg">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01">
            </path>
        </svg>
        <span class="text-[10px] uppercase font-bold tracking-wide">Asignaciones</span>
    </a>

    {{-- Perfil (o Dashboard) --}}
    <a href="{{ route('profile.edit') }}"
        class="flex flex-col items-center justify-center w-full h-full py-1 {{ request()->routeIs('profile.*') ? 'text-blue-600 dark:text-blue-400' : 'text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200' }}">
        <svg class="w-7 h-7 mb-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"
            xmlns="http://www.w3.org/2000/svg">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
        </svg>
        <span class="text-[10px] uppercase font-bold tracking-wide">Perfil</span>
    </a>
</nav>