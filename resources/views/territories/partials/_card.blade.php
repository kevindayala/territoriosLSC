@php
    $isAssigned = $territory->is_assigned > 0;
    $monthsDiff = $territory->last_completed_at ? $territory->last_completed_at->diffInMonths(now()) : null;
    $priorityClass = 'border-l-4 border-t border-r border-b border-gray-200 dark:border-gray-700';

    $borderColor = '#d1d5db'; // default gray
    $warningType = 'none';
    $warningText = '';

    if ($isAssigned) {
        $priority = 5;
        $priorityLabel = __('Territorios asignados');
        $headerColor = 'text-gray-500 dark:text-gray-400 border-gray-200 dark:border-gray-700';

        $borderColor = '#d1d5db'; // gray-300
        $warningType = 'gray';
        $warningText = __('Este territorio esta asignado.');
    } elseif (!$territory->last_completed_at) {
        $priority = 1;
        $priorityLabel = __('Nunca realizados');
        $headerColor = 'text-green-700 dark:text-green-400 border-green-200 dark:border-green-800/50';

        $borderColor = '#22c55e'; // green-500
        $warningType = 'green';
        $warningText = __('Se recomienda hacer este territorio.');
    } elseif ($monthsDiff >= 6) {
        $priority = 2;
        $priorityLabel = __('Prioridad Alta (≥ 6 meses)');
        $headerColor = 'text-green-700 dark:text-green-400 border-green-200 dark:border-green-800/50';

        $borderColor = '#22c55e'; // green-500
        $warningType = 'green';
        $warningText = __('Se recomienda hacer este territorio.');
    } elseif ($monthsDiff >= 2) {
        $priority = 3;
        $priorityLabel = __('Prioridad Media (2 - 6 meses)');
        $headerColor = 'text-yellow-600 dark:text-yellow-500 border-yellow-200 dark:border-yellow-800/50';

        $borderColor = '#eab308'; // yellow-500
        $warningType = 'yellow';
        $warningText = __('Este territorio se hizo hace algunos meses.');
    } else {
        $priority = 4;
        $priorityLabel = __('Recientes (< 2 meses)');
        $headerColor = 'text-red-500 dark:text-red-400 border-red-200 dark:border-red-800/50';

        $borderColor = '#ef4444'; // red-500
        $warningType = 'red';
        $warningText = __('Este territorio se realizó recientemente.');
    }
@endphp

<div class="flex flex-col h-full">
    <a href="{{ route('territories.show', $territory) }}"
        class="group flex flex-col flex-1 bg-white dark:bg-gray-800 rounded-xl p-4 shadow-sm hover:shadow-md hover:-translate-y-0.5 transition-all duration-300 relative active:scale-[0.99] {{ $priorityClass }}">

        <!-- 1) Header row -->
        <div class="flex justify-between items-start gap-3 mb-2">
            <h3 class="text-lg font-bold text-gray-900 dark:text-white leading-tight pt-0.5 break-words"
                style="word-break: break-word;">
                {{ $territory->neighborhood_name }}
            </h3>

            @if ($isAssigned)
                @php
                    $assignment = $territory->assignments->first();
                    $assignedBy = $assignment && $assignment->assignedBy ? $assignment->assignedBy->name : __('Sistema');
                    $assignedTo = $assignment && $assignment->assignedTo ? $assignment->assignedTo->name : null;
                    $isPersonal = $assignment && $assignment->type === 'personal';
                @endphp
                @if($isPersonal)
                    <span
                        class="inline-flex items-center justify-center flex-shrink-0 px-3 py-1 text-xs font-normal tracking-wider text-amber-700 bg-amber-100 rounded-full dark:bg-amber-500/30 dark:text-amber-200 border border-amber-200 dark:border-amber-400/60 shadow-sm whitespace-nowrap">
                        @if(auth()->check() && auth()->user()->hasRole('admin') && $assignedTo)
                            <span title="{{ $assignedTo }}"><span class="font-bold">Territorio:</span>
                                {{ explode(' ', $assignedTo)[0] }}</span>
                        @else
                            <span><span class="font-bold">Territorio</span> Personal</span>
                        @endif
                    </span>
                @else
                    <span
                        class="inline-flex items-center flex-shrink-0 px-3 py-1 rounded-full text-xs font-normal dark:bg-blue-900/30 dark:text-blue-400 whitespace-nowrap"
                        style="background-color: #eff6ff; color: #1d4ed8;">
                        <span class="font-bold">{{ __('Capitán:') }}</span>&nbsp;{{ $assignedBy }}
                    </span>
                @endif
            @endif
        </div>

        <!-- 2) Subtitle row -->
        <div class="text-[13px] text-gray-500 dark:text-gray-400 mb-4">
            {{ $territory->code }} &middot; {{ $territory->city->name }}
        </div>
        <!-- 3) Metrics row -->
        <div class="flex flex-col gap-1.5 text-[13px] text-gray-600 dark:text-gray-300 mb-4">
            <!-- Persons -->
            <div class="flex items-center gap-3">
                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z">
                    </path>
                </svg>
                @if($territory->persons_count === 0)
                    <span class="text-gray-400 italic">{{ __('Sin personas registradas') }}</span>
                @else
                    <span><span class="font-bold">{{ __('Personas:') }}</span> {{ $territory->persons_count }}</span>
                @endif
            </div>

            <!-- Last Done -->
            @php
                $lastDateText = '-';
                if ($territory->last_completed_at) {
                    if ($territory->last_completed_at->isToday()) {
                        $lastDateText = __('Hoy');
                    } elseif ($territory->last_completed_at->isYesterday()) {
                        $lastDateText = __('Ayer');
                    } else {
                        $lastDateText = $territory->last_completed_at->gt(now())
                            ? __('Hace un momento')
                            : ucfirst($territory->last_completed_at->diffForHumans());
                    }
                }
            @endphp
            <div class="flex items-center gap-3">
                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <span>
                    <span class="font-bold">{{ __('Última vez realizado:') }}</span>
                    @if(!$territory->last_completed_at)
                        <span class="font-bold text-gray-900 dark:text-gray-100">{{ $lastDateText }}</span>
                    @else
                        {{ $lastDateText }}
                    @endif
                </span>
            </div>

            <!-- Completions this year -->
            <div class="flex items-center gap-3">
                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15">
                    </path>
                </svg>
                @php
                    $annualCount = $territory->annual_completions_count ?? 0;
                @endphp
                <span><span class="font-bold">{{ __('Realizado este año:') }}</span> {{ $annualCount }}
                    {{ $annualCount == 1 ? __('vez') : __('veces') }}</span>
            </div>
        </div>

        <!-- Priority Warning -->
        @hasanyrole('admin|capitan')
        @if($warningType !== 'none')
            @php
                $bgStyle = '';
                $borderStyle = '';
                $textStyle = '';
                if ($warningType === 'red') {
                    $bgStyle = '#fee2e2';
                    $borderStyle = '#ef4444';
                    $textStyle = '#9f1239';
                } elseif ($warningType === 'yellow') {
                    $bgStyle = '#fef9c3';
                    $borderStyle = '#eab308';
                    $textStyle = '#854d0e';
                } elseif ($warningType === 'gray') {
                    $bgStyle = '#f3f4f6';
                    $borderStyle = '#9ca3af';
                    $textStyle = '#374151';
                } else { // green
                    $bgStyle = '#dcfce7';
                    $borderStyle = '#22c55e';
                    $textStyle = '#166534';
                }
            @endphp
            <div class="mb-4 flex items-start gap-3 px-4 py-3 text-sm font-medium dark:bg-gray-800 dark:border-gray-500 dark:text-gray-300 transition-colors"
                style="background-color: {{ $bgStyle }}; border-left: 6px solid {{ $borderStyle }}; color: {{ $textStyle }}; border-radius: 0 8px 8px 0;">
                <svg class="shrink-0" width="18" height="18" style="margin-top: 1px;" fill="none" stroke="currentColor"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z">
                    </path>
                </svg>
                <span class="leading-snug">{!! $warningText !!}</span>
            </div>
        @endif
        @endhasanyrole

        <!-- 4) Primary action -->
        <div class="mt-auto">
            <div
                class="flex items-center justify-center w-full h-10 bg-blue-600 hover:bg-blue-700 text-white text-sm font-bold rounded-lg shadow-sm transition-all active:scale-[0.98]">
                {{ __('Ver territorio') }}
            </div>
        </div>
    </a>
</div>