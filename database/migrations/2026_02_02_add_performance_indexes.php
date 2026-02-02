<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  /**
   * Run the migrations.
   */
  public function up(): void
  {
    // Índices para optimizar queries de movimientos
    Schema::table('movimientos', function (Blueprint $table) {
      // Índice compuesto: semestre_id + estatus para filtrar rápidamente
      $table->index(['semestre_id', 'estatus'], 'movimientos_semestre_id_estatus_index');

      // Índice: user_id + semestre_id para conteos por usuario
      $table->index(['user_id', 'semestre_id'], 'movimientos_user_id_semestre_id_index');

      // Índice: grupo_id + semestre_id
      $table->index(['grupo_id', 'semestre_id'], 'movimientos_grupo_id_semestre_id_index');

      // Índice: is_paralelo para filtrar rápidamente
      $table->index('is_paralelo', 'movimientos_is_paralelo_index');
    });

    // Índices para optimizar queries de grupos
    Schema::table('grupos', function (Blueprint $table) {
      // Índice compuesto: materia_id + semestre_id
      $table->index(['materia_id', 'semestre_id'], 'grupos_materia_id_semestre_id_index');

      // Índice: semestre_id solo para filtros rápidos
      $table->index('semestre_id', 'grupos_semestre_id_index');
    });

    // Índices para la tabla pivot carrera_user
    if (Schema::hasTable('carrera_user')) {
      Schema::table('carrera_user', function (Blueprint $table) {
        $table->index('user_id', 'carrera_user_user_id_index');
        $table->index('carrera_id', 'carrera_user_carrera_id_index');
      });
    }

    // Índices para la tabla users
    Schema::table('users', function (Blueprint $table) {
      // Índice: rol para filtros rápidos
      $table->index('rol', 'users_rol_index');
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::table('movimientos', function (Blueprint $table) {
      $table->dropIndexIfExists('movimientos_semestre_id_estatus_index');
      $table->dropIndexIfExists('movimientos_user_id_semestre_id_index');
      $table->dropIndexIfExists('movimientos_grupo_id_semestre_id_index');
      $table->dropIndexIfExists('movimientos_is_paralelo_index');
    });

    Schema::table('grupos', function (Blueprint $table) {
      $table->dropIndexIfExists('grupos_materia_id_semestre_id_index');
      $table->dropIndexIfExists('grupos_semestre_id_index');
    });

    if (Schema::hasTable('carrera_user')) {
      Schema::table('carrera_user', function (Blueprint $table) {
        $table->dropIndexIfExists('carrera_user_user_id_index');
        $table->dropIndexIfExists('carrera_user_carrera_id_index');
      });
    }

    Schema::table('users', function (Blueprint $table) {
      $table->dropIndexIfExists('users_rol_index');
    });
  }
};
