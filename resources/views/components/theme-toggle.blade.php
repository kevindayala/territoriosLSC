@props(['align' => 'right', 'display' => 'icon'])

<div x-data="{ 
    themeOpen: false, 
    theme: localStorage.getItem('theme') || 'system',
    
    init() {
        window.addEventListener('theme-changed', (e) => {
            this.theme = e.detail;
        });
    },

    setTheme(val) {
        this.theme = val;
        
        if (val === 'dark') {
            document.documentElement.classList.add('dark');
            localStorage.setItem('theme', 'dark');
        } else if (val === 'light') {
            document.documentElement.classList.remove('dark');
            localStorage.setItem('theme', 'light');
        } else {
            localStorage.removeItem('theme');
            if (window.matchMedia('(prefers-color-scheme: dark)').matches) {
                document.documentElement.classList.add('dark');
            } else {
                document.documentElement.classList.remove('dark');
            }
        }
        
        window.dispatchEvent(new CustomEvent('theme-changed', { detail: val }));
        this.themeOpen = false;
    }
}" class="{{ $display === 'icon' ? 'relative inline-block' : 'w-full' }}">

    {{-- Trigger Button --}}
    @if($display === 'icon')
        <button @click="themeOpen = !themeOpen" type="button"
            class="p-2 text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 focus:outline-none">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path>
            </svg>
        </button>

    @elseif($display === 'menu-item')
        <button @click="themeOpen = true; $dispatch('close')" type="button"
            class="w-full text-start flex items-center gap-2 px-4 py-2 text-sm leading-5 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800 focus:outline-none focus:bg-gray-100 dark:focus:bg-gray-800 transition duration-150 ease-in-out">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path>
            </svg>
            {{ __('Apariencia') }}
        </button>

    @elseif($display === 'responsive')
        <button @click="themeOpen = true; open = false" type="button"
            class="w-full text-start flex items-center gap-2 ps-3 pe-4 py-2 border-l-4 border-transparent text-base font-medium text-gray-600 dark:text-gray-400 hover:text-gray-800 dark:hover:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700 hover:border-gray-300 dark:hover:border-gray-600 focus:outline-none focus:text-gray-800 dark:focus:text-gray-200 focus:bg-gray-50 dark:focus:bg-gray-700 focus:border-gray-300 dark:focus:border-gray-600 transition duration-150 ease-in-out">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path>
            </svg>
            {{ __('Apariencia') }}
        </button>
    @endif

    {{-- Modal and Backdrop teleported to body to avoid clipping and stacking issues --}}
    <template x-teleport="body">
        <div x-show="themeOpen" class="fixed inset-0 z-[5000] flex flex-col justify-end sm:justify-center p-0 sm:p-4"
            style="display: none;">
            {{-- Backdrop --}}
            <div x-show="themeOpen" x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0" @click="themeOpen = false"
                class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm"></div>

            {{-- Modal / Bottom Sheet --}}
            <div x-show="themeOpen" x-trap.noscroll="themeOpen"
                x-transition:enter="transition ease-out duration-300 transform"
                x-transition:enter-start="translate-y-full sm:translate-y-4 sm:opacity-0 sm:scale-95"
                x-transition:enter-end="translate-y-0 sm:translate-y-0 sm:opacity-100 sm:scale-100"
                x-transition:leave="transition ease-in duration-200 transform"
                x-transition:leave-start="translate-y-0 sm:translate-y-0 sm:opacity-100 sm:scale-100"
                x-transition:leave-end="translate-y-full sm:translate-y-4 sm:opacity-0 sm:scale-95"
                class="relative bg-white dark:bg-gray-800 rounded-t-3xl sm:rounded-2xl shadow-2xl w-full max-w-md mx-auto overflow-hidden pb-safe flex flex-col"
                @click.stop>

                <div
                    class="px-4 py-4 border-b border-gray-100 dark:border-gray-700 flex justify-between items-center bg-white dark:bg-gray-800">
                    <h3 class="font-bold text-xl text-gray-900 dark:text-gray-100">{{ __('Selecciona una apariencia') }}
                    </h3>
                    <button @click="themeOpen = false"
                        class="text-gray-400 hover:text-gray-500 dark:hover:text-gray-300 rounded-full p-2 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12">
                            </path>
                        </svg>
                    </button>
                </div>

                <div class="p-3 space-y-2">
                    <button @click="setTheme('light')"
                        class="w-full flex items-center justify-between px-4 py-4 rounded-xl transition-all duration-200 group"
                        :class="theme === 'light' ? 'bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700/50'">
                        <div class="flex items-center gap-4">
                            <div class="p-2 rounded-lg"
                                :class="theme === 'light' ? 'bg-blue-100 dark:bg-blue-800 text-blue-600' : 'bg-gray-100 dark:bg-gray-700 text-gray-500 group-hover:bg-white dark:group-hover:bg-gray-600'">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z">
                                    </path>
                                </svg>
                            </div>
                            <span class="font-bold text-base">{{ __('Claro') }}</span>
                        </div>
                        <div x-show="theme === 'light'"
                            class="flex items-center justify-center w-6 h-6 rounded-full bg-blue-600 dark:bg-blue-500 shadow-sm">
                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M5 13l4 4L19 7"></path>
                            </svg>
                        </div>
                    </button>

                    <button @click="setTheme('dark')"
                        class="w-full flex items-center justify-between px-4 py-4 rounded-xl transition-all duration-200 group"
                        :class="theme === 'dark' ? 'bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700/50'">
                        <div class="flex items-center gap-4">
                            <div class="p-2 rounded-lg"
                                :class="theme === 'dark' ? 'bg-blue-100 dark:bg-blue-800 text-blue-600' : 'bg-gray-100 dark:bg-gray-700 text-gray-500 group-hover:bg-white dark:group-hover:bg-gray-600'">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z">
                                    </path>
                                </svg>
                            </div>
                            <span class="font-bold text-base">{{ __('Oscuro') }}</span>
                        </div>
                        <div x-show="theme === 'dark'"
                            class="flex items-center justify-center w-6 h-6 rounded-full bg-blue-600 dark:bg-blue-500 shadow-sm">
                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M5 13l4 4L19 7"></path>
                            </svg>
                        </div>
                    </button>

                    <button @click="setTheme('system')"
                        class="w-full flex items-center justify-between px-4 py-4 rounded-xl transition-all duration-200 group"
                        :class="theme === 'system' ? 'bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700/50'">
                        <div class="flex items-center gap-4">
                            <div class="p-2 rounded-lg"
                                :class="theme === 'system' ? 'bg-blue-100 dark:bg-blue-800 text-blue-600' : 'bg-gray-100 dark:bg-gray-700 text-gray-500 group-hover:bg-white dark:group-hover:bg-gray-600'">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z">
                                    </path>
                                </svg>
                            </div>
                            <span class="font-bold text-base">{{ __('Sistema') }}</span>
                        </div>
                        <div x-show="theme === 'system'"
                            class="flex items-center justify-center w-6 h-6 rounded-full bg-blue-600 dark:bg-blue-500 shadow-sm">
                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M5 13l4 4L19 7"></path>
                            </svg>
                        </div>
                    </button>
                </div>
            </div>
        </div>
    </template>
</div>