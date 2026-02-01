<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Monitor de queries para detectar problemas N+1 y patrones ineficientes.
 * Se utiliza en desarrollo/testing para identificar consultas problemáticas.
 */
class QueryMonitor
{
  private static array $queries = [];
  private static array $queryPatterns = [];
  private static bool $enabled = false;

  /**
   * Inicializa el monitor de queries
   */
  public static function enable(): void
  {
    if (self::$enabled) {
      return;
    }

    self::$enabled       = true;
    self::$queries       = [];
    self::$queryPatterns = [];

    // Listener para todas las queries ejecutadas
    DB::listen(function ($query) {
      self::recordQuery($query);
    });
  }

  /**
   * Registra una query ejecutada
   */
  private static function recordQuery(object $query): void
  {
    $sql  = $query->sql;
    $time = $query->time;

    self::$queries[] = [
      'sql' => $sql,
      'bindings' => $query->bindings,
      'time' => $time,
      'trace' => debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 15),
    ];

    // Normalizar query para detectar patrones
    $normalized = self::normalizeQuery($sql);
    if (!isset(self::$queryPatterns[$normalized])) {
      self::$queryPatterns[$normalized] = 0;
    }
    self::$queryPatterns[$normalized]++;
  }

  /**
   * Normaliza una query SQL removiendo valores específicos
   */
  private static function normalizeQuery(string $sql): string
  {
    return preg_replace(
      ['/\d+/', '/\'[^\']*\'/', '/\s+/'],
      ['?', "'?'", ' '],
      strtolower(trim($sql))
    );
  }

  /**
   * Detecta patrones N+1: queries similares ejecutadas múltiples veces
   * 
   * @param int $threshold Número mínimo de repeticiones para alertar
   * @return array
   */
  public static function detectNPlusOne(int $threshold = 5): array
  {
    $suspects = [];

    foreach (self::$queryPatterns as $normalized => $count) {
      if ($count >= $threshold) {
        // Filtra queries de framework (migrations, información_schema, etc.)
        if (preg_match('/migration|information_schema|pragma|sqlite_master/i', $normalized)) {
          continue;
        }

        $suspects[$normalized] = $count;
      }
    }

    return $suspects;
  }

  /**
   * Obtiene reporte detallado de N+1
   */
  public static function getNPlusOneReport(): array
  {
    $suspects = self::detectNPlusOne(3);

    $report = [];
    foreach ($suspects as $normalized => $count) {
      // Buscar ejemplo real de esta query normalizada
      $example = null;
      foreach (self::$queries as $q) {
        if (self::normalizeQuery($q['sql']) === $normalized) {
          $example = $q;
          break;
        }
      }

      if ($example) {
        $report[] = [
          'pattern' => $normalized,
          'count' => $count,
          'example_sql' => $example['sql'],
          'example_time_ms' => $example['time'],
          'file' => self::getCallerFile($example['trace']),
        ];
      }
    }

    return $report;
  }

  /**
   * Extrae el archivo llamador desde el stack trace
   */
  private static function getCallerFile(array $trace): string
  {
    foreach ($trace as $frame) {
      if (isset($frame['file']) && !preg_match('/vendor|framework/i', $frame['file'])) {
        return basename($frame['file']) . ':' . ($frame['line'] ?? '?');
      }
    }
    return 'unknown';
  }

  /**
   * Log de alertas N+1 si se detectan
   */
  public static function logNPlusOneIssues(): void
  {
    $report = self::getNPlusOneReport();

    if (empty($report)) {
      return;
    }

    Log::warning('N+1 Query Pattern Detected', [
      'total_suspect_queries' => count($report),
      'issues' => $report,
    ]);
  }

  /**
   * Obtiene estadísticas generales de queries
   */
  public static function getStats(): array
  {
    $totalTime   = 0;
    $slowQueries = [];

    foreach (self::$queries as $q) {
      $totalTime += $q['time'];
      if ($q['time'] > 100) { // queries > 100ms
        $slowQueries[] = [
          'sql' => substr($q['sql'], 0, 100) . '...',
          'time_ms' => $q['time'],
        ];
      }
    }

    return [
      'total_queries' => count(self::$queries),
      'unique_patterns' => count(self::$queryPatterns),
      'total_time_ms' => $totalTime,
      'avg_time_ms' => count(self::$queries) > 0 ? $totalTime / count(self::$queries) : 0,
      'slow_queries_count' => count($slowQueries),
      'slow_queries_sample' => array_slice($slowQueries, 0, 5),
    ];
  }

  /**
   * Reset de estadísticas
   */
  public static function reset(): void
  {
    self::$queries       = [];
    self::$queryPatterns = [];
  }
}
