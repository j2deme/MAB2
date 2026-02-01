<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Services\QueryMonitor;

/**
 * Middleware para monitorear queries N+1 en desarrollo/testing.
 * Se habilita cuando APP_DEBUG=true
 */
class MonitorQueries
{
    public function handle(Request $request, Closure $next)
    {
        // Solo en desarrollo y testing
        if (!config('app.debug') || app()->isProduction()) {
            return $next($request);
        }

        QueryMonitor::enable();
        QueryMonitor::reset();

        $response = $next($request);

        // Log de alertas N+1 al finalizar la request
        QueryMonitor::logNPlusOneIssues();

        // Opcionalmente, adjuntar estadísticas a respuesta (desarrollo)
        if ($request->expectsJson()) {
            $response->header('X-DB-Queries', QueryMonitor::getStats()['total_queries']);
        }

        return $response;
    }
}
