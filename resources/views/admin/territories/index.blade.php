<x-app-layout>
    <x-slot name="title">Territorios Admin</x-slot>
    {{-- El logo ahora reemplaza al botón de volver --}}
    <x-slot name="logo_url">{{ route('admin.settings') }}</x-slot>

    <div class="pt-6 pb-12 md:py-12"
        x-data="{ selectedId: null, showFilters: {{ (request('city_id') || request('filter') || (request('sort') && request('sort') != 'code')) ? 'true' : 'false' }} }">
        {{-- Título de la página --}}
        <x-slot name="header">
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                    {{ __('Territorios') }}
                </h2>
                <a href="{{ route('territories.create', ['redirect_to' => 'admin']) }}"
                    class="inline-flex items-center px-4 h-10 bg-blue-600 hover:bg-blue-700 text-white rounded-full shadow-lg shadow-blue-500/20 active:scale-95 transition-all gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 4v16m8-8H4" />
                    </svg>
                    <span class="text-sm font-bold hidden sm:inline">Registrar Territorio</span>
                    <span class="text-sm font-bold sm:hidden">Nuevo</span>
                </a>
            </div>
        </x-slot>

        {{-- Top Navigation & Search --}}
        <div class="mt-4 sm:mt-6 mb-8">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

                {{-- Filters & Search Row --}}
                <div class="mb-8">
                    <form method="GET" action="{{ route('admin.territories.index') }}" class="w-full">
                        {{-- Clean Search Bar --}}
                        <div class="border-b border-gray-200 dark:border-gray-700 pb-3 mb-2">
                            <div class="flex items-center w-full">
                                <input type="text" name="search" id="search" value="{{ request('search') }}"
                                    placeholder="{{ __('Buscar código o barrio') }}"
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
                                        <option value="code" {{ request('sort') == 'code' ? 'selected' : '' }}>
                                            {{ __('Código') }}
                                        </option>
                                        <option value="date_asc" {{ request('sort') == 'date_asc' ? 'selected' : '' }}>
                                            {{ __('Más tiempo sin trabajar') }}
                                        </option>
                                        <option value="date_desc" {{ request('sort') == 'date_desc' || !request('sort') ? 'selected' : '' }}>
                                            {{ __('Trabajados recientemente') }}
                                        </option>
                                    </select>
                                </div>

                                {{-- Availability Filter --}}
                                <div class="w-full">
                                    <label
                                        class="block text-[13px] font-bold text-[#334155] dark:text-gray-400 tracking-wide mb-3">{{ __('Filtrar por') }}</label>
                                    <div class="flex flex-col gap-3.5">

                                        <div class="mb-2 w-full pr-4">
                                            <label
                                                class="block text-[12px] font-semibold text-[#64748b] dark:text-gray-400 mb-1.5">{{ __('Completados en:') }}</label>
                                            <select name="filter[]" onchange="this.form.submit()"
                                                class="block w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-[#0f172a] dark:text-gray-200 text-[14px] focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 py-2 px-3 shadow-sm transition-colors cursor-pointer">
                                                <option value="">{{ __('Cualquier fecha') }}</option>
                                                <option value="completed_today" {{ in_array('completed_today', (array) request('filter')) ? 'selected' : '' }}>{{ __('Hoy') }}</option>
                                                <option value="completed_this_week" {{ in_array('completed_this_week', (array) request('filter')) ? 'selected' : '' }}>
                                                    {{ __('Esta semana') }}
                                                </option>
                                                <option value="completed_this_month" {{ in_array('completed_this_month', (array) request('filter')) ? 'selected' : '' }}>{{ __('Este mes') }}
                                                </option>
                                            </select>
                                        </div>

                                        <label class="group flex items-center gap-3 cursor-pointer w-max">
                                            <input type="checkbox" name="filter[]" value="recommended"
                                                onchange="this.form.submit()" {{ in_array('recommended', (array) request('filter')) ? 'checked' : '' }}
                                                class="w-[18px] h-[18px] rounded border-gray-300 dark:border-gray-600 text-blue-600 focus:ring-blue-500/30 dark:bg-gray-800 transition-colors cursor-pointer bg-white shadow-sm">
                                            <span class="text-[15px] text-[#0f172a] dark:text-gray-200 select-none">
                                                {{ __('Ver sugeridos') }}
                                            </span>
                                        </label>

                                        <label class="group flex items-center gap-3 cursor-pointer w-max">
                                            <input type="checkbox" name="filter[]" value="assigned"
                                                onchange="this.form.submit()" {{ in_array('assigned', (array) request('filter')) ? 'checked' : '' }}
                                                class="w-[18px] h-[18px] rounded border-gray-300 dark:border-gray-600 text-blue-600 focus:ring-blue-500/30 dark:bg-gray-800 transition-colors cursor-pointer bg-white shadow-sm">
                                            <span class="text-[15px] text-[#0f172a] dark:text-gray-200 select-none">
                                                {{ __('Ver asignados') }}
                                            </span>
                                        </label>

                                        <label class="group flex items-center gap-3 cursor-pointer w-max">
                                            <input type="checkbox" name="filter[]" value="available"
                                                onchange="this.form.submit()" {{ in_array('available', (array) request('filter')) ? 'checked' : '' }}
                                                class="w-[18px] h-[18px] rounded border-gray-300 dark:border-gray-600 text-blue-600 focus:ring-blue-500/30 dark:bg-gray-800 transition-colors cursor-pointer bg-white shadow-sm">
                                            <span class="text-[15px] text-[#0f172a] dark:text-gray-200 select-none">
                                                {{ __('Ver disponibles') }}
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

        {{-- Contenido Principal --}}
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Mobile Card View -->
            <div class="block md:hidden w-full space-y-4">
                @forelse($territories as $territory)
                    <div
                        class="bg-white dark:bg-gray-800 p-5 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm">
                        <div class="mb-5">
                            <div class="flex justify-between items-start gap-3">
                                <div class="flex-1 min-w-0">
                                    <h3
                                        class="font-bold text-[15px] text-gray-900 dark:text-white leading-tight break-words">
                                        {{ $territory->code }} - {{ $territory->neighborhood_name ?? '-' }}
                                    </h3>
                                    <p
                                        class="text-[11px] text-gray-500 dark:text-gray-400 font-medium mt-1 flex flex-wrap items-center gap-x-1.5 uppercase tracking-wide">
                                        <span>{{ $territory->city->name ?? '-' }}</span>
                                        @php
                                            $isAssigned = $territory->assignments->whereNull('completed_at')->isNotEmpty();
                                            $monthsSince = $territory->last_completed_at ? (int) $territory->last_completed_at->diffInMonths(now()) : null;

                                            $statusColor = 'text-gray-500 dark:text-gray-400';
                                            $statusText = '';

                                            if ($isAssigned) {
                                                $statusColor = 'text-gray-400 dark:text-gray-600';
                                            } elseif ($monthsSince === null || $monthsSince >= 6) {
                                                $statusColor = 'text-gray-600 dark:text-gray-300 font-bold';
                                                $statusText = $monthsSince === null ? 'Nunca realizado' : "Hace $monthsSince meses";
                                            } elseif ($monthsSince >= 2) {
                                                $statusColor = 'text-gray-500 dark:text-gray-400 font-medium';
                                                $statusText = "Hace $monthsSince meses";
                                            } else {
                                                $statusColor = 'text-gray-500 dark:text-gray-400';
                                                $statusText = $territory->last_completed_at->format('d/m/Y');
                                            }
                                        @endphp

                                        @if ($statusText)
                                            <span class="text-gray-300 font-normal">•</span>
                                            <span class="{{ $statusColor }}">{{ $statusText }}</span>
                                        @endif
                                    </p>
                                </div>
                                <div class="flex flex-col items-end flex-shrink-0 pt-0.5">
                                    @php
                                        $activeAssignment = $territory->assignments->whereNull('completed_at')->first();
                                        $isAssigned = !is_null($activeAssignment);
                                    @endphp
                                    <div
                                        class="inline-flex items-center px-2 py-0.5 rounded-full {{ $isAssigned ? 'bg-red-50 dark:bg-red-900/20' : 'bg-green-50 dark:bg-green-900/20' }} border {{ $isAssigned ? 'border-red-100 dark:border-red-800' : 'border-green-100 dark:border-green-800' }}">
                                        <div
                                            class="h-1.5 w-1.5 rounded-full me-1.5 {{ $isAssigned ? 'bg-red-500' : 'bg-green-500' }}">
                                        </div>
                                        <span
                                            class="text-[9px] font-black uppercase tracking-wider {{ $isAssigned ? 'text-red-600 dark:text-red-400' : 'text-green-600 dark:text-green-400' }}">
                                            {{ $isAssigned ? 'Asignado' : 'Disponible' }}
                                        </span>
                                    </div>
                                    @if ($isAssigned && $activeAssignment->assignedTo)
                                        <span
                                            class="text-[9px] text-gray-500 dark:text-gray-400 font-bold uppercase mt-1.5 tracking-tighter">{{ $activeAssignment->assignedTo->name }}</span>
                                        @if ($activeAssignment->type === 'personal')
                                            <span
                                                class="text-[8px] bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400 px-1.5 rounded border border-amber-200 dark:border-amber-800/50 mt-1 font-black uppercase">PERSONAL</span>
                                        @endif
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="flex gap-2">
                            <a href="{{ route('territories.show', $territory) }}"
                                class="flex-1 flex items-center justify-center px-4 py-2 bg-blue-50 hover:bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:hover:bg-blue-900/50 dark:text-blue-300 text-sm font-bold rounded-xl transition-colors border border-blue-100 dark:border-blue-800/50">
                                <svg class="w-4 h-4 me-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                                Ver
                            </a>
                            <a href="{{ route('territories.edit', $territory) }}"
                                class="flex-1 flex items-center justify-center px-4 py-2 bg-white hover:bg-gray-50 text-gray-700 dark:bg-gray-800 dark:hover:bg-gray-700/50 dark:text-gray-300 text-sm font-bold rounded-xl transition-colors border border-gray-200 dark:border-gray-700">
                                <svg class="w-4 h-4 me-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                </svg>
                                Editar
                            </a>
                            <form id="delete-form-mobile-{{ $territory->id }}"
                                action="{{ route('territories.destroy', $territory) }}" method="POST" class="inline-block">
                                @csrf
                                @method('DELETE')
                                <button type="button"
                                    @click="selectedId = {{ $territory->id }}; $dispatch('open-modal', 'confirm-delete-mobile')"
                                    class="p-2 bg-red-50 dark:bg-red-900/20 text-red-600 border border-red-100 dark:border-red-900/30 rounded-xl transition-colors">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                            </form>
                        </div>
                    </div>
                @empty
                    <div
                        class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-sm text-center text-gray-500 dark:text-gray-400 border border-gray-100 dark:border-gray-700">
                        No hay territorios registrados.
                    </div>
                @endforelse
            </div>

            <!-- Desktop Table View -->
            <div
                class="hidden md:block bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-2xl overflow-hidden shadow-sm">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-50/50 dark:bg-gray-900/50 border-b border-gray-200 dark:border-gray-700">
                            <th
                                class="px-6 py-4 text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                Código</th>
                            <th
                                class="px-6 py-4 text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                Ciudad</th>
                            <th
                                class="px-6 py-4 text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                Barrio</th>
                            <th
                                class="px-6 py-4 text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                Última vez</th>
                            <th
                                class="px-6 py-4 text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                Estado</th>
                            <th
                                class="px-6 py-4 text-right text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                        @forelse($territories as $territory)
                            <tr
                                class="bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-slate-900/60 transition-colors group">
                                <td class="px-6 py-4">
                                    <div
                                        class="text-sm font-bold text-gray-900 dark:text-white group-hover:dark:text-white">
                                        {{ $territory->code }}
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm text-gray-600 dark:text-gray-300 group-hover:dark:text-white">
                                        {{ $territory->city->name ?? '-' }}
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm text-gray-600 dark:text-gray-300 group-hover:dark:text-white">
                                        {{ $territory->neighborhood_name ?? '-' }}
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    @php
                                        $activeAssignment = $territory->assignments->whereNull('completed_at')->first();
                                        $isAssigned = !is_null($activeAssignment);
                                        $monthsSince = $territory->last_completed_at ? (int) $territory->last_completed_at->diffInMonths(now()) : null;
                                        $dateColorClass = 'text-gray-500 dark:text-gray-400';

                                        if ($isAssigned) {
                                            $dateColorClass = 'text-gray-400 dark:text-gray-600';
                                        } elseif ($monthsSince === null || $monthsSince >= 6) {
                                            $dateColorClass = 'text-gray-600 dark:text-gray-300 font-semibold';
                                        } elseif ($monthsSince >= 2) {
                                            $dateColorClass = 'text-gray-500 dark:text-gray-400 font-medium';
                                        } else {
                                            $dateColorClass = 'text-gray-500 dark:text-gray-400';
                                        }
                                    @endphp
                                    <div class="text-sm {{ $dateColorClass }} group-hover:dark:text-white">
                                        {{ $territory->last_completed_at ? $territory->last_completed_at->format('d/m/Y') : 'Nunca' }}
                                    </div>
                                    @if ($isAssigned && $activeAssignment->assignedTo)
                                        <div
                                            class="mt-1 text-[10px] text-gray-500 dark:text-gray-400 font-bold uppercase tracking-tight">
                                            {{ $activeAssignment->assignedTo->name }}
                                            @if ($activeAssignment->type === 'personal')
                                                <span
                                                    class="ml-1 text-[8px] text-amber-600 dark:text-amber-500 font-black">(Personal)</span>
                                            @endif
                                        </div>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    @php
                                        $activeAssignment = $territory->assignments->whereNull('completed_at')->first();
                                        $isAssigned = !is_null($activeAssignment);
                                    @endphp
                                    <div class="flex items-center">
                                        <div
                                            class="h-2 w-2 rounded-full me-2 {{ $isAssigned ? 'bg-red-500 shadow-[0_0_8px_rgba(239,68,68,0.4)]' : 'bg-green-500 shadow-[0_0_8px_rgba(34,197,94,0.4)]' }}">
                                        </div>
                                        <span
                                            class="text-sm font-medium {{ $isAssigned ? 'text-red-700 dark:text-red-400' : 'text-green-700 dark:text-green-400' }}">
                                            {{ $isAssigned ? 'Asignado' : 'Disponible' }}
                                        </span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <div class="flex justify-end gap-2">
                                        <a href="{{ route('territories.show', $territory) }}" title="Ver territorio"
                                            class="inline-flex p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 dark:text-gray-500 dark:hover:text-gray-300 dark:hover:bg-gray-800 rounded-lg transition-colors">
                                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                        </a>
                                        <a href="{{ route('territories.edit', $territory) }}" title="Editar territorio"
                                            class="inline-flex p-2 text-blue-500 hover:text-blue-700 hover:bg-blue-50 dark:text-blue-400 dark:hover:text-blue-300 dark:hover:bg-blue-500/20 rounded-lg transition-colors">
                                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                        </a>
                                        <form id="delete-form-desktop-{{ $territory->id }}"
                                            action="{{ route('territories.destroy', $territory) }}" method="POST"
                                            class="inline-block">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button"
                                                @click="selectedId = {{ $territory->id }}; $dispatch('open-modal', 'confirm-delete-desktop')"
                                                class="inline-flex p-2 text-red-500 hover:text-red-700 hover:bg-red-50 dark:text-red-400 dark:hover:text-red-300 dark:hover:bg-red-500/20 rounded-lg transition-colors">
                                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-12 text-center text-gray-500 dark:text-gray-400">
                                    No se encontraron territorios registrados.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-6">
                {{ $territories->links() }}
            </div>

            <x-modal-confirm name="confirm-delete-mobile" title="Eliminar Territorio"
                content="¿Estás seguro de eliminar este territorio? Esta acción no se puede deshacer.">
                <x-slot name="footer">
                    <x-secondary-button x-on:click="$dispatch('close')">
                        Cancelar
                    </x-secondary-button>

                    <x-danger-button class="ml-3"
                        x-on:click="document.getElementById('delete-form-mobile-' + selectedId).submit()">
                        Sí, eliminar
                    </x-danger-button>
                </x-slot>
            </x-modal-confirm>

            <x-modal-confirm name="confirm-delete-desktop" title="Eliminar Territorio"
                content="¿Estás seguro de eliminar este territorio? Esta acción no se puede deshacer.">
                <x-slot name="footer">
                    <x-secondary-button x-on:click="$dispatch('close')">
                        Cancelar
                    </x-secondary-button>

                    <x-danger-button class="ml-3"
                        x-on:click="document.getElementById('delete-form-desktop-' + selectedId).submit()">
                        Sí, eliminar
                    </x-danger-button>
                </x-slot>
            </x-modal-confirm>

        </div>
    </div>
</x-app-layout>