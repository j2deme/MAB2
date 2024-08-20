<?php

use App\Enums\MovesType;
use App\Enums\MovesStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('movimientos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained();
            $table->foreignId('semestre_id')->constrained();
            $table->foreignId('carrera_id')->nullable()->constrained();
            $table->foreignId('grupo_id')->constrained();
            $table->enum('tipo', array_column(MovesType::cases(), 'value'));
            $table->enum('estatus', array_column(MovesStatus::cases(), 'value'))->default(MovesStatus::REGISTRADO->value);
            $table->string('motivo');
            $table->string('motivo_adicional')->nullable();
            $table->string('respuesta')->nullable();
            $table->string('respuesta_adicional')->nullable();
            $table->foreignId('asociado_id')->nullable()->constrained('movimientos');
            $table->boolean('is_paralelo')->default(false);
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('movimientos');
    }
};
