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
   * Invalida el caché del semestre activo y stats relacionados
   * Útil cuando se cambia el semestre activo o se actualizan movimientos
   * 
   * @param int|null $semestreId ID del semestre a invalidar (si null, invalida todos)
   * @return void
   */
  protected function invalidarCacheSemestre(?int $semestreId = null): void
  {
    Cache::forget('semestre_activo');
    Cache::forget('semestre_activo_id');

    // Invalidar caches de stats si se proporciona ID específico
    if ($semestreId) {
      Cache::forget("semestre:{$semestreId}:top-carreras");
      Cache::forget("semestre:{$semestreId}:stats");
    }
  }

  /**
   * Invalida caché de conteos para una materia
   * Útil cuando se crean/actualizan movimientos de una materia
   * 
   * @param int $materiaId ID de la materia
   * @return void
   */
  protected function invalidarCacheMateria(int $materiaId): void
  {
    Cache::forget("materia:{$materiaId}:stats");
    Cache::forget("materia:{$materiaId}:estudiantes");
  }

  /**
   * Invalida caché de conteos para un grupo
   * Útil cuando se crean/actualizan movimientos de un grupo
   * 
   * @param int $grupoId ID del grupo
   * @return void
   */
  protected function invalidarCacheGrupo(int $grupoId): void
  {
    Cache::forget("grupo:{$grupoId}:stats");
    Cache::forget("grupo:{$grupoId}:estudiantes");
  }
}

