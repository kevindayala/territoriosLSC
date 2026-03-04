<x-guest-layout>
    <!-- Header/Title -->
    <div class="mb-10 text-center sm:text-left">
        <h2 class="text-3xl font-extrabold text-gray-900 dark:text-white tracking-tight">
            ¡Bienvenido de nuevo!
        </h2>
        <p class="mt-3 text-sm text-gray-500 dark:text-gray-400">
            Ingresa tus credenciales para acceder a tu cuenta.
        </p>
    </div>

    <!-- Session Status -->
    <x-auth-session-status
        class="mb-6 p-4 rounded-lg bg-green-50 text-green-700 dark:bg-green-900/30 dark:text-green-400 border border-green-200 dark:border-green-800"
        :status="session('status')" />

    <form method="POST" action="{{ route('login') }}" class="space-y-6">
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

        <!-- Password -->
        <div x-data="{ show: false }">
            <div class="flex items-center justify-between mb-2">
                <x-input-label for="password" :value="__('Contraseña')"
                    class="text-sm font-semibold text-gray-700 dark:text-gray-300" />
                @if (Route::has('password.request'))
                    <a href="{{ route('password.request') }}"
                        class="text-sm text-indigo-600 dark:text-indigo-400 hover:text-indigo-500 dark:hover:text-indigo-300 font-medium transition-colors">
                        ¿Olvidaste tu contraseña?
                    </a>
                @endif
            </div>

            <div class="relative group">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="h-5 w-5 text-gray-400 dark:text-gray-500" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                    </svg>
                </div>

                <input id="password"
                    class="pl-10 block w-full pe-12 bg-gray-50 dark:bg-gray-900 border-gray-200 dark:border-gray-700 focus:bg-white focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 rounded-xl transition-all duration-200 dark:text-white"
                    x-bind:type="show ? 'text' : 'password'" name="password" required autocomplete="current-password"
                    placeholder="••••••••" />

                <button type="button" @click="show = !show"
                    class="absolute inset-y-0 right-0 px-3 flex items-center text-gray-400 hover:text-indigo-500 transition-colors focus:outline-none"
                    tabindex="-1">
                    <svg x-show="!show" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                    </svg>
                    <svg x-show="show" x-cloak class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                        x-transition:enter="transition ease-out duration-200"
                        x-transition:enter-start="opacity-0 scale-90">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.04m2.458-2.388A9.974 9.974 0 0112 5c4.478 0 8.268 2.943 9.542 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21m-2.101-2.101L3 3m11.101 11.101L10 10" />
                    </svg>
                </button>
            </div>
            <x-input-error :messages="$errors->get('password')" class="mt-2 text-sm text-red-600 dark:text-red-400" />
        </div>

        <!-- Remember Me -->
        <div class="flex items-center justify-between mt-6">
            <label for="remember_me" class="inline-flex items-center group cursor-pointer">
                <div class="relative flex items-center">
                    <input id="remember_me" type="checkbox"
                        class="peer h-5 w-5 cursor-pointer appearance-none rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-indigo-600 checked:border-indigo-600 checked:bg-indigo-600 focus:outline-none focus:ring-2 focus:ring-indigo-600 focus:ring-offset-2 dark:focus:ring-offset-gray-900 transition-all"
                        name="remember">
                    <svg class="absolute left-1/2 top-1/2 -translate-x-1/2 -translate-y-1/2 h-3.5 w-3.5 opacity-0 peer-checked:opacity-100 text-white pointer-events-none transition-opacity"
                        fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                    </svg>
                </div>
                <span
                    class="ms-3 text-sm font-medium text-gray-600 dark:text-gray-400 group-hover:text-gray-900 dark:group-hover:text-gray-200 transition-colors">
                    {{ __('Recuérdame') }}
                </span>
            </label>
        </div>

        <!-- Submit Button -->
        <div class="mt-8">
            <button type="submit"
                class="w-full flex justify-center items-center py-3.5 px-4 border border-transparent rounded-xl shadow-sm text-sm font-bold text-white bg-gray-900 hover:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-900 dark:bg-gray-100 dark:text-gray-800 dark:hover:bg-white dark:focus:ring-offset-gray-900 transition-all duration-200 transform hover:-translate-y-0.5 shadow-gray-900/30 hover:shadow-gray-900/50">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                    xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1">
                    </path>
                </svg>
                {{ __('INICIAR SESIÓN') }}
            </button>
        </div>

        @if(\App\Models\Setting::get('public_registration', 'true') === 'true')
            <div class="mt-6 text-center">
                <p class="text-sm text-gray-600 dark:text-gray-400">
                    ¿No tienes cuenta?
                    <a href="{{ route('register') }}"
                        class="font-semibold text-indigo-600 hover:text-indigo-500 dark:text-indigo-400 dark:hover:text-indigo-300 transition-colors">
                        Regístrate aquí
                    </a>
                </p>
            </div>
        @endif
    </form>
</x-guest-layout>