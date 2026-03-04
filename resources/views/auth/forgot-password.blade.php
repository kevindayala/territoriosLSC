<x-guest-layout>
    <!-- Header/Title -->
    <div class="mb-8 text-center sm:text-left">
        <h2 class="text-3xl font-extrabold text-gray-900 dark:text-white tracking-tight">
            Recuperar Contraseña
        </h2>
        <p class="mt-3 text-sm text-gray-500 dark:text-gray-400">
            {{ __('¿Olvidaste tu contraseña? No hay problema. Solo dinos tu dirección de correo electrónico y te enviaremos un enlace de restablecimiento de contraseña que te permitirá elegir una nueva.') }}
        </p>
    </div>

    <!-- Session Status -->
    <x-auth-session-status
        class="mb-6 p-4 rounded-lg bg-green-50 text-green-700 dark:bg-green-900/30 dark:text-green-400 border border-green-200 dark:border-green-800"
        :status="session('status')" />

    <form method="POST" action="{{ route('password.email') }}" class="space-y-6">
        @csrf

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
                <!-- Replaced x-text-input with standard input to fully control styling -->
                <input id="email"
                    class="pl-10 block w-full bg-gray-50 dark:bg-gray-900 border-gray-200 dark:border-gray-700 focus:bg-white focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 rounded-xl transition-all duration-200 dark:text-white"
                    type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username"
                    placeholder="ejemplo@correo.com" />
            </div>
            <x-input-error :messages="$errors->get('email')" class="mt-2 text-sm text-red-600 dark:text-red-400" />
        </div>

        <div class="flex flex-col sm:flex-row items-center justify-between gap-4 mt-8">
            <a href="{{ route('login') }}"
                class="text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 font-medium transition-colors order-2 sm:order-1">
                &larr; {{ __('Volver al inicio de sesión') }}
            </a>

            <button type="submit"
                class="w-full sm:w-auto flex justify-center items-center py-3 px-6 border border-transparent rounded-xl shadow-sm text-sm font-bold text-white bg-gray-900 hover:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-900 dark:bg-gray-100 dark:text-gray-800 dark:hover:bg-white dark:focus:ring-offset-gray-900 transition-all duration-200 transform hover:-translate-y-0.5 shadow-gray-900/30 hover:shadow-gray-900/50 order-1 sm:order-2">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                    xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z">
                    </path>
                </svg>
                {{ __('ENVIAR ENLACE') }}
            </button>
        </div>
    </form>
</x-guest-layout>