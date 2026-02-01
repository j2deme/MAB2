<?php

namespace App\Traits;

use App\Models\Semestre;
use Illuminate\Support\Facades\Cache;

trait UsesSemestreActivo
{
  /**
   * Obtiene el semestre activo desde caché
   * 
   * @return Semestre|null
   */
  protected function getSemestreActivo(): ?Semestre
  {
    $value = Cache::remember('semestre_activo', 3600, function () {
      return Semestre::where('activo', true)->first();
    });

    return $value instanceof Semestre ? $value : null;
  }

  /**
   * Obtiene solo el ID del semestre activo desde caché
   * 
   * @return int|null
   */
  protected function getSemestreActivoId(): ?int
  {
    $id = Cache::remember('semestre_activo_id', 3600, function () {
      return Semestre::where('activo', true)->value('id');
    });

    return is_null($id) ? null : (int) $id;
  }

  /**
   * Invalida el caché del semestre activo
   * Útil cuando se cambia el semestre activo
   * 
   * @return void
   */
  protected function invalidarCacheSemestre(): void
  {
    Cache::forget('semestre_activo');
    Cache::forget('semestre_activo_id');
  }
}
