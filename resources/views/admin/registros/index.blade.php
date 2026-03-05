<x-app-layout>
    <x-slot name="title">Registros Admin</x-slot>
    <x-slot name="logo_url">{{ route('admin.settings') }}</x-slot>

    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4" x-data="{}">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight text-center sm:text-left">
                {{ __('Registro de Territorios') }}
            </h2>
            <div class="flex items-center gap-2 sm:gap-3 w-full sm:w-auto flex-wrap justify-center sm:justify-end">
                {{-- Backup: Exportar --}}
                <a href="{{ route('admin.registros.export-backup') }}"
                    class="inline-flex items-center justify-center px-3 sm:px-4 h-11 sm:h-10 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-full shadow-sm hover:bg-gray-50 dark:hover:bg-gray-700 active:scale-95 transition-all gap-1.5 sm:gap-2"
                    title="Descargar backup de los registros de asignaciones">
                    <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                        </path>
                    </svg>
                    <span class="text-sm font-bold whitespace-nowrap">Exportar</span>
                </a>

                {{-- Backup: Importar --}}
                <button type="button" x-on:click="$dispatch('open-modal', 'import-backup-modal')"
                    class="inline-flex items-center justify-center px-3 sm:px-4 h-11 sm:h-10 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-full shadow-sm hover:bg-gray-50 dark:hover:bg-gray-700 active:scale-95 transition-all gap-1.5 sm:gap-2"
                    title="Restaurar territorios desde un backup">
                    <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12">
                        </path>
                    </svg>
                    <span class="text-sm font-bold whitespace-nowrap">Importar</span>
                </button>

                {{-- Exportar S-13 --}}
                <button type="button" x-on:click="$dispatch('open-modal', 's13-export-modal')"
                    class="inline-flex items-center justify-center px-3 sm:px-4 h-11 sm:h-10 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-full shadow-sm hover:bg-gray-50 dark:hover:bg-gray-700 active:scale-95 transition-all gap-1.5 sm:gap-2">
                    <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                        </path>
                    </svg>
                    <span class="text-sm font-bold whitespace-nowrap">S-13</span>
                </button>

                {{-- Nuevo Registro --}}
                <a href="{{ route('admin.registros.create') }}"
                    class="inline-flex items-center justify-center px-3 sm:px-4 h-11 sm:h-10 bg-blue-600 hover:bg-blue-700 text-white rounded-full shadow-lg shadow-blue-500/20 active:scale-95 transition-all gap-1.5 sm:gap-2">
                    <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 4v16m8-8H4" />
                    </svg>
                    <span class="text-sm font-bold whitespace-nowrap">Nuevo Registro</span>
                </a>
            </div>
        </div>
    </x-slot>

    <div class="pt-6 pb-12 md:py-12" x-data="{ 
        selectedId: null,
        showFilters: {{ (request('city_id') || request('filter') || (request('sort') && request('sort') != 'id')) ? 'true' : 'false' }} 
    }">
        {{-- Top Navigation & Search --}}
        <div class="mt-4 sm:mt-6 mb-8">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

                {{-- Flash Messages --}}
                @if (session('success'))
                    <div class="mb-6 p-4 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-xl flex items-start gap-3"
                        x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 6000)"
                        x-transition:leave="transition ease-in duration-300"
                        x-transition:leave-start="opacity-100 translate-y-0"
                        x-transition:leave-end="opacity-0 -translate-y-2">
                        <svg class="w-5 h-5 text-green-500 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                clip-rule="evenodd" />
                        </svg>
                        <div class="flex-1">
                            <p class="text-sm font-bold text-green-800 dark:text-green-300">{{ session('success') }}</p>
                        </div>
                        <button @click="show = false" class="text-green-400 hover:text-green-600">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                                    clip-rule="evenodd" />
                            </svg>
                        </button>
                    </div>
                @endif
                @if (session('error'))
                    <div class="mb-6 p-4 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-xl flex items-start gap-3"
                        x-data="{ show: true }" x-show="show" x-transition:leave="transition ease-in duration-300"
                        x-transition:leave-start="opacity-100 translate-y-0"
                        x-transition:leave-end="opacity-0 -translate-y-2">
                        <svg class="w-5 h-5 text-red-500 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                                clip-rule="evenodd" />
                        </svg>
                        <div class="flex-1">
                            <p class="text-sm font-bold text-red-800 dark:text-red-300">{{ session('error') }}</p>
                        </div>
                        <button @click="show = false" class="text-red-400 hover:text-red-600">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                                    clip-rule="evenodd" />
                            </svg>
                        </button>
                    </div>
                @endif

                {{-- Filters & Search Row --}}
                <div class="mb-8">
                    <form method="GET" action="{{ route('admin.registros.index') }}" class="w-full">
                        {{-- Clean Search Bar --}}
                        <div class="border-b border-gray-200 dark:border-gray-700 pb-3 mb-2">
                            <div class="flex items-center w-full">
                                <input type="text" name="search" value="{{ request('search') }}"
                                    placeholder="{{ __('Buscar por territorio o usuario') }}"
                                    class="flex-1 border border-gray-400 dark:border-gray-600 rounded bg-white dark:bg-gray-800 focus:ring-1 focus:ring-gray-500 focus:border-gray-500 text-[15px] placeholder-gray-500 dark:placeholder-gray-400 text-gray-800 dark:text-gray-200 px-3 py-1.5 outline-none shadow-sm">

                                <div class="flex items-center gap-3 pl-3">
                                    <button type="button" @click="showFilters = !showFilters"
                                        class="text-black dark:text-gray-200 hover:text-gray-700 dark:hover:text-white transition-colors focus:outline-none flex-shrink-0">
                                        <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                            stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                            <line x1="4" y1="21" x2="4" y2="14" />
                                            <line x1="4" y1="10" x2="4" y2="3" />
                                            <line x1="12" y1="21" x2="12" y2="12" />
                                            <line x1="12" y1="8" x2="12" y2="3" />
                                            <line x1="20" y1="21" x2="20" y2="16" />
                                            <line x1="20" y1="12" x2="20" y2="3" />
                                            <line x1="1" y1="14" x2="7" y2="14" />
                                            <line x1="9" y1="8" x2="15" y2="8" />
                                            <line x1="17" y1="16" x2="23" y2="16" />
                                        </svg>
                                    </button>

                                    <button type="submit"
                                        class="text-black dark:text-gray-200 hover:text-gray-700 dark:hover:text-white transition-colors focus:outline-none flex-shrink-0">
                                        <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                            stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                            <circle cx="11" cy="11" r="8"></circle>
                                            <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>

                        {{-- Collapsible Filters --}}
                        <div x-show="showFilters" x-transition:enter="transition ease-out duration-200"
                            x-transition:enter-start="opacity-0 -translate-y-2"
                            x-transition:enter-end="opacity-100 translate-y-0"
                            x-transition:leave="transition ease-in duration-150"
                            x-transition:leave-start="opacity-100 translate-y-0"
                            x-transition:leave-end="opacity-0 -translate-y-2"
                            class="mt-4 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl p-6 shadow-sm"
                            style="display: none;">
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                {{-- City Selection --}}
                                <div class="w-full">
                                    <label
                                        class="block text-[13px] font-bold text-[#334155] dark:text-gray-400 tracking-wide mb-2">{{ __('Ciudad') }}</label>
                                    <select name="city_id" onchange="this.form.submit()"
                                        class="block w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-[#0f172a] dark:text-gray-200 text-[15px] font-medium focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 py-2.5 pl-3.5 pr-10 shadow-sm transition-colors cursor-pointer">
                                        <option value="">{{ __('Todas las ciudades') }}</option>
                                        @foreach ($cities as $city)
                                            <option value="{{ $city->id }}" {{ request('city_id') == $city->id ? 'selected' : '' }}>
                                                {{ $city->display_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                {{-- Sort Filter --}}
                                <div class="w-full">
                                    <label
                                        class="block text-[13px] font-bold text-[#334155] dark:text-gray-400 tracking-wide mb-2">{{ __('Ordenar por') }}</label>
                                    <select name="sort" id="sort" onchange="this.form.submit()"
                                        class="block w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-[#0f172a] dark:text-gray-200 text-[15px] font-medium focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 py-2.5 pl-3.5 pr-10 shadow-sm transition-colors cursor-pointer">
                                        <option value="id" {{ request('sort') == 'id' || !request('sort') ? 'selected' : '' }}>
                                            {{ __('Recientes') }}
                                        </option>
                                        <option value="code" {{ request('sort') == 'code' ? 'selected' : '' }}>
                                            {{ __('Código') }}
                                        </option>
                                        <option value="date_desc" {{ request('sort') == 'date_desc' ? 'selected' : '' }}>
                                            {{ __('Fecha asignación (Recientes)') }}
                                        </option>
                                        <option value="date_asc" {{ request('sort') == 'date_asc' ? 'selected' : '' }}>
                                            {{ __('Fecha asignación (Antiguos)') }}
                                        </option>
                                    </select>
                                </div>

                                {{-- Availability Filter --}}
                                <div class="w-full">
                                    <label
                                        class="block text-[13px] font-bold text-[#334155] dark:text-gray-400 tracking-wide mb-3">{{ __('Filtrar por') }}</label>
                                    <div class="flex flex-col gap-3.5">
                                        <label class="group flex items-center gap-3 cursor-pointer w-max">
                                            <input type="checkbox" name="filter[]" value="recommended"
                                                onchange="this.form.submit()" {{ in_array('recommended', (array) request('filter')) ? 'checked' : '' }}
                                                class="w-[18px] h-[18px] rounded border-gray-300 dark:border-gray-600 text-blue-600 focus:ring-blue-500/30 dark:bg-gray-800 transition-colors cursor-pointer bg-white shadow-sm">
                                            <span class="text-[15px] text-[#0f172a] dark:text-gray-200 select-none">
                                                {{ __('Territorios sugeridos') }}
                                            </span>
                                        </label>

                                        <label class="group flex items-center gap-3 cursor-pointer w-max">
                                            <input type="checkbox" name="filter[]" value="assigned"
                                                onchange="this.form.submit()" {{ in_array('assigned', (array) request('filter')) ? 'checked' : '' }}
                                                class="w-[18px] h-[18px] rounded border-gray-300 dark:border-gray-600 text-blue-600 focus:ring-blue-500/30 dark:bg-gray-800 transition-colors cursor-pointer bg-white shadow-sm">
                                            <span class="text-[15px] text-[#0f172a] dark:text-gray-200 select-none">
                                                {{ __('Territorios asignados') }}
                                            </span>
                                        </label>

                                        <label class="group flex items-center gap-3 cursor-pointer w-max">
                                            <input type="checkbox" name="filter[]" value="available"
                                                onchange="this.form.submit()" {{ in_array('available', (array) request('filter')) ? 'checked' : '' }}
                                                class="w-[18px] h-[18px] rounded border-gray-300 dark:border-gray-600 text-blue-600 focus:ring-blue-500/30 dark:bg-gray-800 transition-colors cursor-pointer bg-white shadow-sm">
                                            <span class="text-[15px] text-[#0f172a] dark:text-gray-200 select-none">
                                                {{ __('Territorios disponibles') }}
                                            </span>
                                        </label>

                                        <label class="group flex items-center gap-3 cursor-pointer w-max">
                                            <input type="checkbox" name="filter[]" value="completed_last_month"
                                                onchange="this.form.submit()" {{ in_array('completed_last_month', (array) request('filter')) ? 'checked' : '' }}
                                                class="w-[18px] h-[18px] rounded border-gray-300 dark:border-gray-600 text-blue-600 focus:ring-blue-500/30 dark:bg-gray-800 transition-colors cursor-pointer bg-white shadow-sm">
                                            <span class="text-[15px] text-[#0f172a] dark:text-gray-200 select-none">
                                                {{ __('Realizados en el último mes') }}
                                            </span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Mobile View -->
            <div class="block md:hidden w-full space-y-4">
                @forelse($registros as $registro)
                    <div
                        class="bg-white dark:bg-gray-800 p-5 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm relative">
                        <div class="mb-4">
                            <div class="flex justify-between items-start gap-3">
                                <div>
                                    <h3 class="font-bold text-[15px] text-gray-900 dark:text-white leading-tight">
                                        Territorio: {{ $registro->territory->code ?? 'N/A' }}
                                    </h3>
                                    <div
                                        class="text-[11px] text-gray-500 dark:text-gray-400 mt-1 uppercase font-bold tracking-tight">
                                        {{ $registro->territory->city->name ?? '-' }} •
                                        {{ $registro->territory->neighborhood_name ?? '-' }}
                                    </div>
                                    <p class="text-[12px] text-gray-500 dark:text-gray-400 font-medium mt-1">
                                        Usuario: {{ $registro->assignedTo->name ?? 'N/A' }}
                                    </p>
                                    <p class="text-[11px] text-gray-500 dark:text-gray-400 mt-1">
                                        Asignado:
                                        {{ $registro->assigned_at ? $registro->assigned_at->format('d/m/Y') : '-' }} <br>
                                        Completado:
                                        {{ $registro->completed_at ? $registro->completed_at->format('d/m/Y') : 'Pendiente' }}
                                    </p>
                                </div>
                                <div class="flex flex-col items-end flex-shrink-0 pt-0.5">
                                    <div
                                        class="inline-flex items-center px-2 py-0.5 rounded-full {{ $registro->completed_at ? 'bg-green-50 dark:bg-green-900/20 text-green-600 dark:text-green-400 border-green-100' : 'bg-red-50 dark:bg-red-900/20 text-red-600 dark:text-red-400 border-red-100' }} border">
                                        <span class="text-[9px] font-black uppercase tracking-wider">
                                            {{ $registro->completed_at ? 'Completado' : 'Pendiente' }}
                                        </span>
                                    </div>
                                    @if($registro->type === 'personal')
                                        <span
                                            class="text-[8px] bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400 px-1.5 rounded border border-amber-200 dark:border-amber-800/50 mt-1.5 font-black uppercase">PERSONAL</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="flex gap-2">
                            <a href="{{ route('admin.registros.edit', $registro) }}"
                                class="flex-1 flex items-center justify-center px-4 py-2 bg-white hover:bg-gray-50 text-gray-700 dark:bg-gray-800 dark:hover:bg-gray-700/50 dark:text-gray-300 text-sm font-bold rounded-xl transition-colors border border-gray-200 dark:border-gray-700">
                                <svg class="w-4 h-4 me-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                </svg>
                                Editar
                            </a>
                            <button type="button"
                                @click="selectedId = {{ $registro->id }}; $dispatch('open-modal', 'confirm-delete-mobile')"
                                class="p-2 bg-red-50 dark:bg-red-900/20 text-red-600 border border-red-100 dark:border-red-900/30 rounded-xl transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                            </button>
                        </div>
                    </div>
                @empty
                    <div
                        class="bg-white dark:bg-gray-800 p-6 rounded-lg text-center text-gray-500 dark:text-gray-400 border border-gray-100 dark:border-gray-700">
                        No hay registros.
                    </div>
                @endforelse
            </div>

            <!-- Desktop View -->
            <div
                class="hidden md:block bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-2xl overflow-hidden shadow-sm">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-50/50 dark:bg-gray-900/50 border-b border-gray-200 dark:border-gray-700">
                            <th class="px-6 py-4 text-xs font-bold text-gray-500 dark:text-gray-400 uppercase">
                                Código</th>
                            <th class="px-6 py-4 text-xs font-bold text-gray-500 dark:text-gray-400 uppercase">
                                Ciudad</th>
                            <th class="px-6 py-4 text-xs font-bold text-gray-500 dark:text-gray-400 uppercase">
                                Barrio</th>
                            <th class="px-6 py-4 text-xs font-bold text-gray-500 dark:text-gray-400 uppercase">Usuario
                            </th>
                            <th class="px-6 py-4 text-xs font-bold text-gray-500 dark:text-gray-400 uppercase">Asignado
                            </th>
                            <th class="px-6 py-4 text-xs font-bold text-gray-500 dark:text-gray-400 uppercase">
                                Completado</th>
                            <th
                                class="px-6 py-4 text-right text-xs font-bold text-gray-500 dark:text-gray-400 uppercase">
                                Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                        @forelse($registros as $registro)
                            <tr class="hover:bg-gray-50 dark:hover:bg-slate-900/60 transition-colors">
                                <td class="px-6 py-4 font-bold text-gray-900 dark:text-white">
                                    {{ $registro->territory->code ?? 'N/A' }}
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-300">
                                    {{ $registro->territory->city->name ?? '-' }}
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-300">
                                    {{ $registro->territory->neighborhood_name ?? '-' }}
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-300">
                                    {{ $registro->assignedTo->name ?? 'N/A' }}
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">
                                    <div class="flex flex-col">
                                        <span>{{ $registro->assigned_at ? $registro->assigned_at->format('d/m/Y') : '-' }}</span>
                                        @if($registro->type === 'personal')
                                            <span
                                                class="text-[10px] font-bold text-amber-600 dark:text-amber-400 uppercase mt-0.5">Territorio
                                                Personal</span>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-sm">
                                    <span
                                        class="{{ $registro->completed_at ? 'text-green-600 dark:text-green-400' : 'text-red-500 dark:text-red-400' }} font-medium">
                                        {{ $registro->completed_at ? $registro->completed_at->format('d/m/Y') : 'Pendiente' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <div class="flex justify-end gap-2">
                                        <a href="{{ route('admin.registros.edit', $registro) }}"
                                            class="p-2 text-blue-500 hover:text-blue-700 hover:bg-blue-50 dark:text-blue-400 dark:hover:bg-blue-500/20 rounded-lg">
                                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                        </a>
                                        <button type="button"
                                            @click="selectedId = {{ $registro->id }}; $dispatch('open-modal', 'confirm-delete-desktop')"
                                            class="p-2 text-red-500 hover:text-red-700 hover:bg-red-50 dark:text-red-400 dark:hover:bg-red-500/20 rounded-lg">
                                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                        <form id="delete-form-desktop-{{ $registro->id }}"
                                            action="{{ route('admin.registros.destroy', $registro) }}" method="POST"
                                            class="hidden">
                                            @csrf
                                            @method('DELETE')
                                        </form>
                                        <form id="delete-form-mobile-{{ $registro->id }}"
                                            action="{{ route('admin.registros.destroy', $registro) }}" method="POST"
                                            class="hidden">
                                            @csrf
                                            @method('DELETE')
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-12 text-center text-gray-500 dark:text-gray-400">
                                    No hay registros.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-6">
                {{ $registros->links() }}
            </div>

            <x-modal-confirm name="confirm-delete-mobile" title="Eliminar Registro"
                content="¿Estás seguro de eliminar este registro? Esto recalculará la última fecha del territorio.">
                <x-slot name="footer">
                    <x-secondary-button x-on:click="$dispatch('close')">Cancelar</x-secondary-button>
                    <x-danger-button class="ml-3"
                        x-on:click="document.getElementById('delete-form-mobile-' + selectedId).submit()">Sí,
                        eliminar</x-danger-button>
                </x-slot>
            </x-modal-confirm>

            <x-modal-confirm name="confirm-delete-desktop" title="Eliminar Registro"
                content="¿Estás seguro de eliminar este registro? Esto recalculará la última fecha del territorio.">
                <x-slot name="footer">
                    <x-secondary-button x-on:click="$dispatch('close')">Cancelar</x-secondary-button>
                    <x-danger-button class="ml-3"
                        x-on:click="document.getElementById('delete-form-desktop-' + selectedId).submit()">Sí,
                        eliminar</x-danger-button>
                </x-slot>
            </x-modal-confirm>
        </div>
    </div>

    <!-- Modal para S-13 -->
    <x-modal name="s13-export-modal" maxWidth="md">
        <div class="p-6 sm:p-8">
            <h2 class="text-xl font-bold text-gray-900 dark:text-gray-100 mb-2 leading-tight">
                Exportar Registro S-13
            </h2>
            <p class="text-sm text-gray-500 dark:text-gray-400 mb-6 leading-relaxed">
                Seleccione el rango de fechas para generar el reporte de asignaciones de territorios.
            </p>

            <div class="space-y-5">
                <div>
                    <label for="s13_start_date"
                        class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Fecha de Inicio</label>
                    <div class="relative">
                        <input type="date" id="s13_start_date" name="start_date"
                            value="{{ \Carbon\Carbon::now()->subYear()->startOfMonth()->format('Y-m-d') }}"
                            class="block w-full px-4 py-3 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-xl text-gray-900 dark:text-white shadow-sm focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition-colors">
                    </div>
                </div>
                <div>
                    <label for="s13_end_date"
                        class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Fecha de Fin</label>
                    <div class="relative">
                        <input type="date" id="s13_end_date" name="end_date"
                            value="{{ \Carbon\Carbon::now()->endOfMonth()->format('Y-m-d') }}"
                            class="block w-full px-4 py-3 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-xl text-gray-900 dark:text-white shadow-sm focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition-colors">
                    </div>
                </div>
            </div>

            <div
                class="mt-8 pt-6 border-t border-gray-100 dark:border-gray-700 flex flex-col-reverse sm:flex-row justify-end gap-3">
                <button type="button" x-on:click="$dispatch('close')"
                    class="w-full sm:w-auto px-5 py-2.5 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-xl hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors text-center">
                    Cancelar
                </button>
                <button type="button" x-on:click="
                        const start = document.getElementById('s13_start_date').value;
                        const end = document.getElementById('s13_end_date').value;
                        if(start && end) {
                            window.open('{{ route('export.assignments') }}?start_date=' + start + '&end_date=' + end, '_blank'); 
                            $dispatch('close');
                        } else {
                            alert('Por favor complete ambas fechas.');
                        }
                   "
                    class="w-full sm:w-auto inline-flex items-center justify-center px-6 py-2.5 bg-blue-600 border border-transparent rounded-xl font-bold text-sm text-white hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 shadow-sm transition-all gap-2">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                        </path>
                    </svg>
                    Generar PDF
                </button>
            </div>
        </div>
    </x-modal>

    <!-- Flatpickr for forced Spanish Locale on Date Inputs -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://npmcdn.com/flatpickr/dist/l10n/es.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            flatpickr("#s13_start_date", {
                locale: "es",
                altInput: true,
                altFormat: "d/m/Y",
                dateFormat: "Y-m-d",
            });
            flatpickr("#s13_end_date", {
                locale: "es",
                altInput: true,
                altFormat: "d/m/Y",
                dateFormat: "Y-m-d",
            });
        });
    </script>
    {{-- Modal Importar Backup --}}
    <x-modal name="import-backup-modal" maxWidth="md">
        <div class="p-6 sm:p-8" x-data="{
            dragOver: false,
            fileName: '',
            fileSize: '',
            fileReady: false,
            submitting: false,
            handleFile(file) {
                const validExtensions = ['.xlsx', '.csv', '.xls'];
                const ext = '.' + file.name.split('.').pop().toLowerCase();
                if (!validExtensions.includes(ext)) {
                    alert('Solo se permiten archivos .xlsx, .csv o .xls');
                    return;
                }
                if (file.size > 5 * 1024 * 1024) {
                    alert('El archivo no debe superar los 5MB');
                    return;
                }
                this.fileName = file.name;
                this.fileSize = (file.size / 1024).toFixed(1) + ' KB';
                this.fileReady = true;
                const dataTransfer = new DataTransfer();
                dataTransfer.items.add(file);
                this.$refs.fileInput.files = dataTransfer.files;
            },
            submitForm() {
                if (!this.fileReady) return;
                this.submitting = true;
                this.$refs.importForm.submit();
            },
            removeFile() {
                this.fileName = '';
                this.fileSize = '';
                this.fileReady = false;
                this.$refs.fileInput.value = '';
            }
        }">
            <h2 class="text-xl font-bold text-gray-900 dark:text-gray-100 mb-2 leading-tight">
                Importar Backup
            </h2>
            <p class="text-sm text-gray-500 dark:text-gray-400 mb-6 leading-relaxed">
                Seleccione un archivo de backup previamente exportado para restaurar los registros de asignaciones de
                territorios.
            </p>

            {{-- Warning --}}
            <div
                class="mb-6 p-3 bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800 rounded-xl flex items-start gap-2.5">
                <svg class="w-5 h-5 text-amber-500 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd"
                        d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"
                        clip-rule="evenodd" />
                </svg>
                <p class="text-xs text-amber-800 dark:text-amber-300 leading-relaxed">
                    <strong>Nota:</strong> La importación actualizará las asignaciones existentes (por territorio,
                    usuario y fecha) y creará
                    las nuevas. Los registros que no estén en el archivo no se eliminarán.
                </p>
            </div>

            <form method="POST" action="{{ route('admin.registros.import') }}" enctype="multipart/form-data"
                x-ref="importForm">
                @csrf

                {{-- Drop Zone --}}
                <div class="relative" @dragover.prevent="dragOver = true" @dragleave.prevent="dragOver = false"
                    @drop.prevent="dragOver = false; if ($event.dataTransfer.files.length) handleFile($event.dataTransfer.files[0])">

                    <template x-if="!fileReady">
                        <label for="import-file-input-registros"
                            class="flex flex-col items-center justify-center w-full h-40 border-2 border-dashed rounded-2xl cursor-pointer transition-all duration-200"
                            :class="dragOver
                                ? 'border-blue-400 bg-blue-50 dark:bg-blue-900/20 dark:border-blue-500'
                                : 'border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-800/50 hover:bg-gray-100 dark:hover:bg-gray-800 hover:border-gray-400 dark:hover:border-gray-500'">
                            <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                <svg class="w-10 h-10 mb-3 transition-colors duration-200"
                                    :class="dragOver ? 'text-blue-400' : 'text-gray-400 dark:text-gray-500'" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                        d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                                </svg>
                                <p class="mb-1 text-sm text-gray-600 dark:text-gray-400">
                                    <span class="font-bold text-blue-600 dark:text-blue-400">Haga clic para
                                        seleccionar</span>
                                    <span class="hidden sm:inline"> o arrastre aquí</span>
                                </p>
                                <p class="text-xs text-gray-500 dark:text-gray-500">.xlsx, .csv, .xls (máx. 5MB)</p>
                            </div>
                        </label>
                    </template>

                    <template x-if="fileReady">
                        <div
                            class="flex items-center w-full p-4 border border-green-200 dark:border-green-800 bg-green-50 dark:bg-green-900/20 rounded-2xl">
                            <div
                                class="flex items-center justify-center w-12 h-12 bg-green-100 dark:bg-green-900/40 rounded-xl mr-4 flex-shrink-0">
                                <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-bold text-gray-900 dark:text-white truncate" x-text="fileName">
                                </p>
                                <p class="text-xs text-gray-500 dark:text-gray-400" x-text="fileSize"></p>
                            </div>
                            <button type="button" @click="removeFile()"
                                class="ml-3 p-1.5 text-gray-400 hover:text-red-500 hover:bg-red-50 dark:hover:bg-red-900/30 rounded-lg transition-colors flex-shrink-0">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                                        clip-rule="evenodd" />
                                </svg>
                            </button>
                        </div>
                    </template>

                    <input type="file" name="file" id="import-file-input-registros" x-ref="fileInput"
                        accept=".xlsx,.csv,.xls" class="hidden"
                        @change="if ($event.target.files.length) handleFile($event.target.files[0])">
                </div>
            </form>

            <div
                class="mt-8 pt-6 border-t border-gray-100 dark:border-gray-700 flex flex-col-reverse sm:flex-row justify-end gap-3">
                <button type="button" x-on:click="$dispatch('close')"
                    class="w-full sm:w-auto px-5 py-2.5 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-xl hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors text-center">
                    Cancelar
                </button>
                <button type="button" @click="submitForm()" :disabled="!fileReady || submitting" :class="fileReady && !submitting
                        ? 'bg-blue-600 hover:bg-blue-700 text-white shadow-sm cursor-pointer'
                        : 'bg-gray-200 dark:bg-gray-700 text-gray-400 dark:text-gray-500 cursor-not-allowed'"
                    class="w-full sm:w-auto inline-flex items-center justify-center px-6 py-2.5 border border-transparent rounded-xl font-bold text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition-all gap-2">
                    <template x-if="!submitting">
                        <span class="inline-flex items-center gap-2">
                            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12">
                                </path>
                            </svg>
                            Importar Backup
                        </span>
                    </template>
                    <template x-if="submitting">
                        <span class="inline-flex items-center gap-2">
                            <svg class="animate-spin h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none"
                                viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                    stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor"
                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                </path>
                            </svg>
                            Importando...
                        </span>
                    </template>
                </button>
            </div>
        </div>
    </x-modal>

</x-app-layout>