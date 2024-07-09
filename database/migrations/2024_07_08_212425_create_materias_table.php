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
        Schema::create('materias', function (Blueprint $table) {
            $table->id();
            $table->string('clave', 15)->unique();
            $table->string('nombre', 100);
            $table->string('nombre_completo');
            $table->foreignId('carrera_id')->constrained();
            $table->integer('semestre')->unsigned()->default(0);
            $table->integer('ht')->unsigned()->default(0);
            $table->integer('hp')->unsigned()->default(0);
            $table->integer('cr')->unsigned()->default(0);
            $table->boolean('activo')->default(false);

            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('materias');
    }
};
