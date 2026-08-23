<?php

namespace App\Observers;

use App\Models\Movimiento;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class MovimientoObserver
{
  /**
   * Handle the Movimiento "created" event.
   */
  public function created(Movimiento $movimiento): void
  {
    $this->invalidateCaches($movimiento);
  }

  /**
   * Handle the Movimiento "updated" event.
   */
  public function updated(Movimiento $movimiento): void
  {
    $this->invalidateCaches($movimiento);
  }

  /**
   * Handle the Movimiento "deleted" event.
   */
  public function deleted(Movimiento $movimiento): void
  {
    $this->invalidateCaches($movimiento);
  }

  /**
   * Handle the Movimiento "restored" event.
   */
  public function restored(Movimiento $movimiento): void
  {
    $this->invalidateCaches($movimiento);
  }

  /**
   * Invalida todos los caches relacionados con este movimiento
   */
  private function invalidateCaches(Movimiento $movimiento): void
  {
    $semestreId = $movimiento->semestre_id;

    $this->forgetCachePattern("lista_materias_sem_{$semestreId}_*");
    $this->forgetCachePattern("lista_materias_counts_sem_{$semestreId}_*");
    $this->forgetCachePattern("lista_generacion_sem_{$semestreId}_*");
    $this->forgetCachePattern("lista_generacion_counts_sem_{$semestreId}_*");
  }

  /**
   * Invalida caches con sufijo variable usando la tabla del store de cache.
   * Este patrón es seguro para cache database y evita dejar entries stale.
   */
  private function forgetCachePattern(string $pattern): void
  {
    $cacheTable  = config('cache.stores.database.table', 'cache');
    $likePattern = str_replace('*', '%', $pattern);

    DB::table($cacheTable)
      ->where('key', 'like', $likePattern)
      ->delete();
  }
}
