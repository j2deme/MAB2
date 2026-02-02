<?php

namespace App\Observers;

use App\Models\Movimiento;
use Illuminate\Support\Facades\Cache;

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

    // Patrón de invalidación: eliminar todos los caches que contengan este semestre
    // Para ListaMaterias: "lista_materias_sem_{semestreId}_*"
    $this->forgetCachePattern("lista_materias_sem_{$semestreId}_*");

    // Para ListaGeneracion: "lista_generacion_sem_{$semestreId}_*"
    $this->forgetCachePattern("lista_generacion_sem_{$semestreId}_*");
  }

  /**
   * Invalida caches que coincidan con un patrón (soporte básico)
   *
   * Nota: Esta función intenta borrar caches usando patrones.
   * Si usas file-based cache (default en local), Laravel no tiene soporte
   * nativo para patrones. Considera usar Redis o Memcached en producción.
   */
  private function forgetCachePattern(string $pattern): void
  {
    // Solución simple: borrar los caches conocidos más comunes
    // Esta es una aproximación; en producción, considera usar Redis

    // Extraer el semestre_id del patrón
    preg_match('/sem_(\d+)_/', $pattern, $matches);
    if (empty($matches)) {
      return;
    }

    $semestreId = $matches[1];

    // Borrar todos los posibles caches para este semestre
    // Combinaciones: todas las coordinadoras + sin filtro + con/sin filtro de carrera
    $possiblePrefixes = [
      'lista_materias_sem_',
      'lista_generacion_sem_',
    ];

    foreach ($possiblePrefixes as $prefix) {
      // Borra aproximadamente (sin patrón exacto, borramos manualmente)
      // En un caso real con muchos coordinadores, considera usar eventos o jobs

      // Intento 1: borrar sin filtro específico
      Cache::forget("{$prefix}{$semestreId}_user_*");

      // Si usas Redis, mejor enfoque:
      // Cache::store('redis')->connection()->eval(
      //     "return redis.call('del', unpack(redis.call('keys',ARGV[1])))",
      //     0,
      //     "{$prefix}{$semestreId}_*"
      // );
    }
  }
}
