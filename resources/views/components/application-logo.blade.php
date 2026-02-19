@php
    $logoSvg = public_path('img/logo.svg');
    $logoPng = public_path('img/logo.png');
@endphp

@if(file_exists($logoSvg))
    <img src="{{ asset('img/logo.svg') }}" {{ $attributes->merge(['alt' => config('app.name'), 'class' => 'h-9 w-auto']) }}>
@elseif(file_exists($logoPng))
    <img src="{{ asset('img/logo.png') }}" {{ $attributes->merge(['alt' => config('app.name'), 'class' => 'h-9 w-auto']) }}>
@else
    <div {{ $attributes->merge(['class' => 'font-black text-2xl tracking-tighter text-blue-600 dark:text-blue-400 flex items-center gap-2']) }}>
        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
        </svg>
        <span>{{ config('app.name') }}</span>
    </div>
@endif