<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Services\QueryMonitor;
use Illuminate\Support\Facades\Auth;

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

        try {
            $response = $next($request);
        } catch (\TypeError $e) {
            // Detectar errores relacionados con method_exists y registrar información útil
            if (str_contains($e->getMessage(), 'method_exists')) {
                \Log::error('TypeError method_exists detected', [
                    'message' => $e->getMessage(),
                    'route' => optional($request->route())->getName(),
                    'uri' => $request->getRequestUri(),
                    'method' => $request->method(),
                    'user_id' => Auth::id(),
                    'trace' => $e->getTraceAsString(),
                ]);
            }

            throw $e;
        }

        // Log de alertas N+1 al finalizar la request
        QueryMonitor::logNPlusOneIssues();

        // Opcionalmente, adjuntar estadísticas a respuesta (desarrollo)
        if ($request->expectsJson()) {
            $response->header('X-DB-Queries', QueryMonitor::getStats()['total_queries']);
        }

        return $response;
    }
}
