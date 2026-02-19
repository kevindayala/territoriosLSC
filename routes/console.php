<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

\Illuminate\Support\Facades\Schedule::call(function () {
    $overdue = \App\Models\TerritoryAssignment::whereNull('completed_at')
        ->whereDate('assigned_at', '<=', now()->subDay())
        ->with('assignedTo')
        ->get();

    foreach ($overdue as $assignment) {
        \Illuminate\Support\Facades\Log::info("Recordatorio: Territorio {$assignment->territory_id} asignado a {$assignment->assignedTo->email} está pendiente.");
    }
})->dailyAt('18:00')->timezone('America/Bogota');

\Illuminate\Support\Facades\Schedule::command('assignments:autocomplete')->hourly();
