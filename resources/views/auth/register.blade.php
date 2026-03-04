<x-guest-layout>
    <!-- Header/Title -->
    <div class="mb-10 text-center sm:text-left">
        <h2 class="text-3xl font-extrabold text-gray-900 dark:text-white tracking-tight">
            Crear Cuenta
        </h2>
        <p class="mt-3 text-sm text-gray-500 dark:text-gray-400">
            Regístrate para comenzar a gestionar tus territorios de manera eficiente.
        </p>
    </div>

    <form method="POST" action="{{ route('register') }}" x-data="{ isSubmitting: false }"
        x-on:submit="if (isSubmitting) { $event.preventDefault(); return; } isSubmitting = true;" class="space-y-6">
        @csrf

        <!-- Name -->
        <div class="relative">
            <x-input-label for="name" :value="__('Nombre')"
                class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2" />
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="h-5 w-5 text-gray-400 dark:text-gray-500" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                </div>
                <input id="name"
                    class="pl-10 block w-full bg-gray-50 dark:bg-gray-900 border-gray-200 dark:border-gray-700 focus:bg-white focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 rounded-xl transition-all duration-200 dark:text-white"
                    type="text" name="name" value="{{ old('name') }}" required autofocus autocomplete="name"
                    placeholder="Tu nombre completo" />
            </div>
            <x-input-error :messages="$errors->get('name')" class="mt-2 text-sm text-red-600 dark:text-red-400" />
        </div>

        <!-- Email Address -->
        <div class="relative">
            <x-input-label for="email" :value="__('Correo Electrónico')"
                class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2" />
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="h-5 w-5 text-gray-400 dark:text-gray-500" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207" />
                    </svg>
                </div>
                <input id="email"
                    class="pl-10 block w-full bg-gray-50 dark:bg-gray-900 border-gray-200 dark:border-gray-700 focus:bg-white focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 rounded-xl transition-all duration-200 dark:text-white"
                    type="email" name="email" value="{{ old('email') }}" required autocomplete="username"
                    placeholder="ejemplo@correo.com" />
            </div>
            <x-input-error :messages="$errors->get('email')" class="mt-2 text-sm text-red-600 dark:text-red-400" />
        </div>

        <!-- Password -->
        <div class="relative">
            <x-input-label for="password" :value="__('Contraseña')"
                class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2" />
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="h-5 w-5 text-gray-400 dark:text-gray-500" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                    </svg>
                </div>
                <input id="password"
                    class="pl-10 block w-full bg-gray-50 dark:bg-gray-900 border-gray-200 dark:border-gray-700 focus:bg-white focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 rounded-xl transition-all duration-200 dark:text-white"
                    type="password" name="password" required autocomplete="new-password" placeholder="••••••••" />
            </div>
            <x-input-error :messages="$errors->get('password')" class="mt-2 text-sm text-red-600 dark:text-red-400" />
        </div>

        <!-- Confirm Password -->
        <div class="relative">
            <x-input-label for="password_confirmation" :value="__('Confirmar Contraseña')"
                class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2" />
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="h-5 w-5 text-gray-400 dark:text-gray-500" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                    </svg>
                </div>
                <input id="password_confirmation"
                    class="pl-10 block w-full bg-gray-50 dark:bg-gray-900 border-gray-200 dark:border-gray-700 focus:bg-white focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 rounded-xl transition-all duration-200 dark:text-white"
                    type="password" name="password_confirmation" required autocomplete="new-password"
                    placeholder="••••••••" />
            </div>
            <x-input-error :messages="$errors->get('password_confirmation')"
                class="mt-2 text-sm text-red-600 dark:text-red-400" />
        </div>

        <div class="mt-8 space-y-4">
            <button type="submit" x-bind:disabled="isSubmitting"
                class="w-full flex justify-center items-center py-3.5 px-4 border border-transparent rounded-xl shadow-sm text-sm font-bold text-white bg-gray-900 hover:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-900 dark:bg-gray-100 dark:text-gray-800 dark:hover:bg-white dark:focus:ring-offset-gray-900 transition-all duration-200 transform hover:-translate-y-0.5 shadow-gray-900/30 hover:shadow-gray-900/50 disabled:opacity-50 disabled:cursor-not-allowed">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                    xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path>
                </svg>
                <span x-text="isSubmitting ? 'REGISTRANDO...' : 'CREAR CUENTA'"></span>
            </button>

            <div class="text-center">
                <p class="text-sm text-gray-600 dark:text-gray-400">
                    ¿Ya tienes una cuenta?
                    <a href="{{ route('login') }}"
                        class="font-semibold text-indigo-600 hover:text-indigo-500 dark:text-indigo-400 dark:hover:text-indigo-300 transition-colors">
                        Inicia sesión aquí
                    </a>
                </p>
            </div>
        </div>
    </form>
</x-guest-layout>