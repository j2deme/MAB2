<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
  /**
   * Run the migrations.
   * Agrega índices para optimizar consultas frecuentes en las vistas de Livewire.
   * Estos índices mejoran performance de filtros, búsquedas y agregaciones.
   */
  public function up(): void
  {
    // Índices para tabla MOVIMIENTOS
    // Usados en: MovimientosTable, Grupos/Show, Materias/Show, búsquedas de estudiantes
    Schema::table('movimientos', function (Blueprint $table) {
      // INDEX: Filtrar movimientos por grupo y estado de soft-delete
      // Queries: withCount(['movimientos as X' => fn($q) => $q->where('grupo_id', X)->whereNull('deleted_at')])
      if (!$this->indexExists('movimientos', 'idx_movimientos_grupo_deleted')) {
        $table->index(['grupo_id', 'deleted_at'], 'idx_movimientos_grupo_deleted');
      }

      // INDEX: Filtrar movimientos por usuario y estado
      // Queries: Búsqueda de movimientos de un estudiante
      if (!$this->indexExists('movimientos', 'idx_movimientos_user_deleted')) {
        $table->index(['user_id', 'deleted_at'], 'idx_movimientos_user_deleted');
      }

      // INDEX: Filtrar por tipo y estatus (análisis de movimientos)
      // Queries: WHERE tipo = 'ALTA' AND estatus = 'AUTORIZADO'
      if (!$this->indexExists('movimientos', 'idx_movimientos_tipo_estatus')) {
        $table->index(['tipo', 'estatus'], 'idx_movimientos_tipo_estatus');
      }

      // INDEX: Búsquedas por semestre (análisis de periodos)
      // Queries: Reportes por semestre
      if (!$this->indexExists('movimientos', 'idx_movimientos_semestre_deleted')) {
        $table->index(['semestre_id', 'deleted_at'], 'idx_movimientos_semestre_deleted');
      }

      // INDEX: Agregaciones con timestamps
      // Queries: ORDER BY updated_at DESC LIMIT 10
      if (!$this->indexExists('movimientos', 'idx_movimientos_updated_at')) {
        $table->index(['updated_at'], 'idx_movimientos_updated_at');
      }
    });

    // Índices para tabla GRUPOS
    // Usados en: GruposTable, Materias/Show, listados de grupos activos
    Schema::table('grupos', function (Blueprint $table) {
      // INDEX: Filtrar grupos por semestre y disponibilidad
      // Queries: Listado de grupos disponibles en semestre activo
      if (!$this->indexExists('grupos', 'idx_grupos_semestre_disponible')) {
        $table->index(['semestre_id', 'is_disponible'], 'idx_grupos_semestre_disponible');
      }

      // INDEX: Filtrar grupos por materia
      // Queries: Todas las secciones de una materia
      if (!$this->indexExists('grupos', 'idx_grupos_materia_id')) {
        $table->index(['materia_id'], 'idx_grupos_materia_id');
      }

      // INDEX: Composición para búsquedas combinadas
      // Queries: WHERE semestre_id = X AND materia_id = Y
      if (!$this->indexExists('grupos', 'idx_grupos_semestre_materia')) {
        $table->index(['semestre_id', 'materia_id'], 'idx_grupos_semestre_materia');
      }
    });

    // Índices para tabla MATERIAS
    // Usados en: MateriasTable, filtros por carrera
    Schema::table('materias', function (Blueprint $table) {
      // INDEX: Filtrar materias por carrera (listados principales)
      // Queries: SELECT * FROM materias WHERE carrera_id = X AND deleted_at IS NULL
      if (!$this->indexExists('materias', 'idx_materias_carrera_deleted')) {
        $table->index(['carrera_id', 'deleted_at'], 'idx_materias_carrera_deleted');
      }

      // INDEX: Búsqueda por estado activo/inactivo
      // Queries: Filtrar materias según disponibilidad académica
      if (!$this->indexExists('materias', 'idx_materias_activo')) {
        $table->index(['activo'], 'idx_materias_activo');
      }

      // INDEX: Búsquedas por clave (importaciones, reportes)
      // Queries: WHERE clave = 'ABC123'
      if (!$this->indexExists('materias', 'idx_materias_clave')) {
        $table->index(['clave'], 'idx_materias_clave');
      }
    });

    // Índices para tabla USERS
    // Usados en: búsquedas de estudiantes, login
    Schema::table('users', function (Blueprint $table) {
      // INDEX: Búsqueda por username (login, búsquedas)
      // Queries: WHERE username LIKE '%?%'
      if (!$this->indexExists('users', 'idx_users_username')) {
        $table->index(['username'], 'idx_users_username');
      }

      // INDEX: Búsqueda por rol (filtros de usuarios)
      // Queries: WHERE rol = 'ESTUDIANTE'
      if (!$this->indexExists('users', 'idx_users_rol')) {
        $table->index(['rol'], 'idx_users_rol');
      }
    });

    // Índices para tabla CARRERA_USER (relación muchos a muchos)
    // Usados en: búsquedas de usuarios por carrera
    Schema::table('carrera_user', function (Blueprint $table) {
      // INDEX: Búsqueda de carreras de un usuario
      // Queries: SELECT * FROM carrera_user WHERE user_id = ?
      if (!$this->indexExists('carrera_user', 'idx_carrera_user_user')) {
        $table->index(['user_id'], 'idx_carrera_user_user');
      }

      // INDEX: Búsqueda de usuarios de una carrera
      // Queries: SELECT * FROM carrera_user WHERE carrera_id = ?
      if (!$this->indexExists('carrera_user', 'idx_carrera_user_carrera')) {
        $table->index(['carrera_id'], 'idx_carrera_user_carrera');
      }
    });

    // Índices para tabla SEMESTRES
    // Usados en: filtros de semestre activo, listados de periodos
    Schema::table('semestres', function (Blueprint $table) {
      // INDEX: Búsqueda por clave (identificación única)
      // Queries: WHERE clave = '2024-01'
      if (!$this->indexExists('semestres', 'idx_semestres_clave')) {
        $table->index(['clave'], 'idx_semestres_clave');
      }

      // INDEX: Filtro por estado activo
      // Queries: WHERE activo = 1
      if (!$this->indexExists('semestres', 'idx_semestres_activo')) {
        $table->index(['activo'], 'idx_semestres_activo');
      }

      // INDEX: Búsqueda por fechas de altas
      // Queries: WHERE fecha >= inicio_altas AND fecha <= fin_altas
      if (!$this->indexExists('semestres', 'idx_semestres_altas')) {
        $table->index(['inicio_altas', 'fin_altas'], 'idx_semestres_altas');
      }
    });

    // Índices para tabla CARRERAS
    // Usados en: filtros de carrera, listados
    Schema::table('carreras', function (Blueprint $table) {
      // INDEX: Búsqueda por siglas (filtros comunes)
      // Queries: WHERE siglas = 'ING'
      if (!$this->indexExists('carreras', 'idx_carreras_siglas')) {
        $table->index(['siglas'], 'idx_carreras_siglas');
      }
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::table('movimientos', function (Blueprint $table) {
      $table->dropIndexIfExists('idx_movimientos_grupo_deleted');
      $table->dropIndexIfExists('idx_movimientos_user_deleted');
      $table->dropIndexIfExists('idx_movimientos_tipo_estatus');
      $table->dropIndexIfExists('idx_movimientos_semestre_deleted');
      $table->dropIndexIfExists('idx_movimientos_updated_at');
    });

    Schema::table('grupos', function (Blueprint $table) {
      $table->dropIndexIfExists('idx_grupos_semestre_disponible');
      $table->dropIndexIfExists('idx_grupos_materia_id');
      $table->dropIndexIfExists('idx_grupos_semestre_materia');
    });

    Schema::table('materias', function (Blueprint $table) {
      $table->dropIndexIfExists('idx_materias_carrera_deleted');
      $table->dropIndexIfExists('idx_materias_activo');
      $table->dropIndexIfExists('idx_materias_clave');
    });

    Schema::table('users', function (Blueprint $table) {
      $table->dropIndexIfExists('idx_users_username');
      $table->dropIndexIfExists('idx_users_rol');
    });

    Schema::table('carrera_user', function (Blueprint $table) {
      $table->dropIndexIfExists('idx_carrera_user_user');
      $table->dropIndexIfExists('idx_carrera_user_carrera');
    });

    Schema::table('semestres', function (Blueprint $table) {
      $table->dropIndexIfExists('idx_semestres_clave');
      $table->dropIndexIfExists('idx_semestres_activo');
      $table->dropIndexIfExists('idx_semestres_altas');
    });

    Schema::table('carreras', function (Blueprint $table) {
      $table->dropIndexIfExists('idx_carreras_siglas');
    });
  }

  /**
   * Verifica si un índice ya existe en una tabla.
   */
  private function indexExists(string $table, string $indexName): bool
  {
    $indexes = DB::select("SHOW INDEXES FROM {$table} WHERE Key_name = ?", [$indexName]);
    return count($indexes) > 0;
  }
};
