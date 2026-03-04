<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;

class AutocompleteAssignmentsMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // Solo verificamos cada 5 minutos para no saturar el servidor en cada recarga de página.
        if (!Cache::has('last_autocomplete_check')) {
            // Ejecuta el comando mágico que hicimos antes en segundo plano real-time
            Artisan::call('assignments:autocomplete');

            // Ponemos un temporizador para no volver a revisar hasta dentro de 5 minutos
            Cache::put('last_autocomplete_check', true, now()->addMinutes(5));
        }

        return $next($request);
    }
}
