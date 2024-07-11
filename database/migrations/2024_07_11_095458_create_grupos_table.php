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
        Schema::create('grupos', function (Blueprint $table) {
            $table->id();
            $table->string('siglas', 5);
            $table->foreignId('semestre_id')->constrained();
            $table->foreignId('materia_id')->constrained();
            $table->boolean('is_disponible')->default(true);
            $table->boolean('is_paralelizable')->default(false);
            $table->softDeletes();
            $table->timestamps();

            $table->unique(['semestre_id', 'materia_id', 'siglas']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('grupos');
    }
};
