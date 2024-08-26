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
        Schema::table('semestres', function (Blueprint $table) {
            $table->datetime('inicio_altas')->change();
            $table->datetime('fin_altas')->change();
            $table->datetime('inicio_bajas')->change();
            $table->datetime('fin_bajas')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('semestres', function (Blueprint $table) {
            $table->date('inicio_altas')->change();
            $table->date('fin_altas')->change();
            $table->date('inicio_bajas')->change();
            $table->date('fin_bajas')->change();
        });
    }
};
