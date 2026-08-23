<?php

namespace App\Services;

use App\Models\Carrera;
use App\Models\Materia;
use App\Models\Movimiento;
use App\Models\Semestre;
use App\Models\User;
use App\Models\Grupo;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class MovimientoFilterService
{
  /**
   * Get cached grupos for a given semestre and optionally filter by carrera
   *
   * @param Semestre $semestre
   * @param array|null $carreras
   * @return mixed
   */
  public static function getGruposForSemestre(Semestre $semestre, ?array $carreras = null)
  {
    $cacheKey = "movimientos.grupos.sem_{$semestre->id}";

    if (!empty($carreras)) {
      $cacheKey .= '_carr_' . implode('_', $carreras);
    }

    return Cache::remember($cacheKey, 3600, function () use ($semestre, $carreras) {
      return Grupo::query()
        ->select('grupos.*')
        ->with('materia:id,nombre_completo,clave,carrera_id')
        ->whereHas('materia', fn($q) => $q->whereIn('semestre_id', [$semestre->id]))
        ->when(!empty($carreras), function ($query) use ($carreras) {
          return $query->whereHas('materia', fn($q) => $q->whereIn('carrera_id', $carreras));
        })
        ->orderBy('siglas')
        ->get();
    });
  }

  /**
   * Get cached estudiantes (users with Estudiante role) filtered by carrier ids
   *
   * @param array|null $carreras
   * @return mixed
   */
  public static function getEstudiantesForCarreras(?array $carreras = null)
  {
    $cacheKey = 'movimientos.estudiantes';

    if (!empty($carreras)) {
      $cacheKey .= '_carr_' . implode('_', $carreras);
    }

    return Cache::remember($cacheKey, 3600, function () use ($carreras) {
      return User::query()
        ->select('id', 'username', 'name')
        ->whereHas('roles', fn($q) => $q->where('name', 'Estudiante'))
        ->when(!empty($carreras), function ($query) use ($carreras) {
          return $query->whereHas('carreras', fn($q) => $q->whereIn('id', $carreras));
        })
        ->orderBy('username')
        ->get();
    });
  }

  /**
   * Get cached carreras for current user (if coordinator) or all active carreras
   *
   * @return mixed
   */
  public static function getCarrerasForUser()
  {
    $userId   = Auth::id();
    $cacheKey = "movimientos.carreras.user_{$userId}";

    return Cache::remember($cacheKey, 3600, function () {
      if (Auth::user()->es('Coordinador')) {
        return Auth::user()->carreras()->select('id', 'nombre', 'siglas', 'color')->get();
      }

      return Carrera::query()
        ->select('id', 'nombre', 'siglas', 'color')
        ->where('activa', true)
        ->orderBy('nombre')
        ->get();
    });
  }

  /**
   * Get cached siglas (group signatures) for a semestre
   *
   * @param Semestre $semestre
   * @return mixed
   */
  public static function getSiglasForSemestre(Semestre $semestre)
  {
    $cacheKey = "movimientos.siglas.sem_{$semestre->id}";

    return Cache::remember($cacheKey, 3600, function () use ($semestre) {
      return Grupo::query()
        ->select('siglas')
        ->whereHas('materia', fn($q) => $q->where('semestre_id', $semestre->id))
        ->distinct()
        ->orderBy('siglas')
        ->pluck('siglas')
        ->toArray();
    });
  }

  /**
   * Invalidate all relevant cache keys for a semestre
   */
  public static function invalidateCacheForSemestre(Semestre $semestre): void
  {
    $cacheTable = config('cache.stores.database.table', 'cache');

    DB::table($cacheTable)
      ->where('key', 'like', "movimientos.%sem_{$semestre->id}%")
      ->delete();
  }

  /**
   * Invalidate cache by specific key pattern
   */
  public static function invalidateCachePattern(string $pattern): void
  {
    $cacheTable  = config('cache.stores.database.table', 'cache');
    $likePattern = str_replace('*', '%', $pattern);

    DB::table($cacheTable)
      ->where('key', 'like', $likePattern)
      ->delete();
  }
}
