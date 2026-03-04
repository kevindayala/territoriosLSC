<x-guest-layout>
    <!-- Header/Title -->
    <div class="mb-8 text-center sm:text-left">
        <h2 class="text-3xl font-extrabold text-gray-900 dark:text-white tracking-tight">
            Verifica tu correo
        </h2>
        <p class="mt-3 text-sm text-gray-500 dark:text-gray-400">
            {{ __('¡Gracias por registrarte! Antes de comenzar, ¿podrías verificar tu dirección de correo electrónico haciendo clic en el enlace que acabamos de enviarte? Si no recibiste el correo, con gusto te enviaremos otro.') }}
        </p>
    </div>

    @if (session('status') == 'verification-link-sent')
        <div
            class="mb-6 p-4 rounded-lg bg-green-50 text-green-700 dark:bg-green-900/30 dark:text-green-400 border border-green-200 dark:border-green-800 text-sm font-medium">
            {{ __('Se ha enviado un nuevo enlace de verificación a la dirección de correo electrónico que proporcionaste durante el registro.') }}
        </div>
    @endif

    <div class="mt-8 flex flex-col sm:flex-row items-center justify-between gap-6">
        <form method="POST" action="{{ route('verification.send') }}" class="w-full sm:w-auto">
            @csrf

            <button type="submit"
                class="w-full sm:w-auto flex justify-center items-center py-3 px-6 border border-transparent rounded-xl shadow-sm text-sm font-bold text-white bg-gray-900 hover:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-900 dark:bg-gray-100 dark:text-gray-800 dark:hover:bg-white dark:focus:ring-offset-gray-900 transition-all duration-200 transform hover:-translate-y-0.5 shadow-gray-900/30 hover:shadow-gray-900/50">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                    xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z">
                    </path>
                </svg>
                {{ __('REENVIAR CORREO') }}
            </button>
        </form>

        <form method="POST" action="{{ route('logout') }}" class="w-full sm:w-auto">
            @csrf

            <button type="submit"
                class="w-full sm:w-auto text-sm font-semibold text-gray-500 hover:text-red-600 dark:text-gray-400 dark:hover:text-red-400 transition-colors flex items-center justify-center">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                    xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1">
                    </path>
                </svg>
                {{ __('Cerrar Sesión') }}
            </button>
        </form>
    </div>
</x-guest-layout>