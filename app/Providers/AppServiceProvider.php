<?php

namespace App\Providers;

use App\Models\Movimiento;
use App\Observers\MovimientoObserver;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Registrar observer para invalidar caches al actualizar movimientos
        Movimiento::observe(MovimientoObserver::class);

        // Límites por IP y por número de control para evitar abuso de correos
        // Por IP el límite es holgado porque las plataformas externas llaman desde
        // un mismo servidor; el límite estricto es por número de control.
        RateLimiter::for('password-forgot', fn(Request $request) => [
            Limit::perMinute(60)->by('ip:' . $request->ip()),
            Limit::perHour(3)->by('user:' . Str::lower((string) $request->input('username'))),
        ]);

        RateLimiter::for('password-reset', fn(Request $request) => [
            Limit::perMinute(60)->by('ip:' . $request->ip()),
            Limit::perHour(10)->by('user:' . Str::lower((string) $request->input('username'))),
        ]);
    }
}
