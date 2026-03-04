<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body
    class="font-sans text-gray-900 antialiased bg-gray-50 dark:bg-gray-900 selection:bg-indigo-500 selection:text-white">
    <div class="min-h-screen flex flex-col sm:flex-row">
        <!-- Left Side / Branding (Hidden on small screens) -->
        <div
            class="hidden sm:flex sm:w-1/2 lg:w-5/12 bg-gradient-to-br from-indigo-900 via-indigo-800 to-slate-900 items-center justify-center relative overflow-hidden">
            <!-- Decorative animated shapes -->
            <div
                class="absolute w-[30rem] h-[30rem] bg-indigo-500/20 rounded-full blur-3xl -top-20 -left-20 animate-[pulse_6s_ease-in-out_infinite]">
            </div>
            <div
                class="absolute w-[25rem] h-[25rem] bg-purple-500/20 rounded-full blur-3xl bottom-10 right-10 animate-[pulse_8s_ease-in-out_infinite]">
            </div>

            <div class="z-10 text-center px-10 md:px-16 lg:px-20 text-white flex flex-col items-center">
                <a href="/" class="group flex flex-col items-center">
                    <div
                        class="bg-white/10 p-6 rounded-3xl backdrop-blur-md border border-white/20 shadow-2xl group-hover:scale-105 transition-transform duration-500">
                        <x-application-logo class="w-24 h-24 fill-current text-white drop-shadow-md" />
                    </div>
                </a>
                <h1
                    class="mt-10 text-4xl md:text-5xl font-extrabold tracking-tight drop-shadow-sm leading-tight text-white">
                    Gestión de <br><span class="text-indigo-300">Territorios</span>
                </h1>


                <!-- decorative dots -->
                <div class="flex gap-2 mt-12 opacity-50">
                    <div class="w-2 h-2 rounded-full bg-white"></div>
                    <div class="w-2 h-2 rounded-full bg-white/50"></div>
                    <div class="w-2 h-2 rounded-full bg-white/30"></div>
                </div>
            </div>
        </div>

        <!-- Right Side / Content Form -->
        <div
            class="w-full sm:w-1/2 lg:w-7/12 flex flex-col justify-center min-h-screen bg-white dark:bg-gray-900 relative">
            <!-- Pattern background for right side -->
            <div
                class="absolute inset-0 bg-[url('data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMjAiIGhlaWdodD0iMjAiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+PGNpcmNsZSBjeD0iMiIgY3k9IjIiIHI9IjIiIGZpbGw9IiNlN2U1ZTIiIGZpbGwtb3BhY2l0eT0iMC40Ii8+PC9zdmc+')] dark:bg-[url('data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMjAiIGhlaWdodD0iMjAiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+PGNpcmNsZSBjeD0iMiIgY3k9IjIiIHI9IjIiIGZpbGw9IiMzNzQxNTEiIGZpbGwtb3BhY2l0eT0iMC40Ii8+PC9zdmc+')] opacity-50 dark:opacity-20 z-0">
            </div>

            <div class="w-full max-w-md mx-auto px-6 sm:px-8 lg:px-12 py-12 z-10">
                <!-- Mobile Logo -->
                <div class="sm:hidden text-center mb-8">
                    <a href="/" class="inline-block">
                        <div
                            class="bg-indigo-50 dark:bg-gray-800 p-4 rounded-2xl border border-indigo-100 dark:border-gray-700 shadow-sm flex items-center justify-center">
                            <x-application-logo class="w-16 h-16 fill-current text-indigo-600 dark:text-indigo-400" />
                        </div>
                    </a>
                </div>

                {{ $slot }}
            </div>
        </div>
    </div>
</body>

</html>