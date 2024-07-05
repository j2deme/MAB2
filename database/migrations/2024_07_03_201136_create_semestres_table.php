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
        Schema::create('semestres', function (Blueprint $table) {
            $table->id();
            $table->string('clave')->unique();
            $table->string('nombre');
            $table->string('nombre_completo');
            $table->date('inicio_altas')->nullable();
            $table->date('fin_altas')->nullable();
            $table->date('inicio_bajas')->nullable();
            $table->date('fin_bajas')->nullable();
            $table->integer('max_altas')->unsigned()->nullable()->default(5);
            $table->boolean('activo')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('semestres');
    }
};
