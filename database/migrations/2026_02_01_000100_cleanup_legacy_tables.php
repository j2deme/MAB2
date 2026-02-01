<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  /**
   * Run the migrations.
   * Limpia tablas legacy relacionadas con la mini app de eventos/actividades/asistencias
   * que fue migrada a una aplicación adicional.
   * 
   * Tablas a eliminar:
   * - eventos
   * - actividades
   * - asistencias
   */
  public function up(): void
  {
    // Deshabilitar verificación de foreign keys temporalmente
    // para evitar conflictos si hay referencias cruzadas
    Schema::disableForeignKeyConstraints();

    // Eliminar tablas en orden inverso de dependencias
    // (si hay FK, eliminar primero las que hacen referencia)
    Schema::dropIfExists('asistencias');
    Schema::dropIfExists('actividades');
    Schema::dropIfExists('eventos');

    // Re-habilitar verificación de foreign keys
    Schema::enableForeignKeyConstraints();
  }

  /**
   * Reverse the migrations.
   * 
   * Nota: Esta es una migración de limpieza. No es posible reconstruir las tablas
   * eliminadas sin los schemas originales. Si necesitas recuperar estas tablas,
   * restaura desde un backup anterior a esta migración.
   */
  public function down(): void
  {
    // Migración de limpieza no reversible
    // Las tablas fueron migradas a otra aplicación
    // No reconstruimos aquí para evitar inconsistencias
  }
};
