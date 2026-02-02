<?php

namespace App\Providers;

use App\Models\Movimiento;
use App\Observers\MovimientoObserver;
use Illuminate\Support\ServiceProvider;

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
    }
}
