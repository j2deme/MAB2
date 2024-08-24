<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Carrera;

class EstudianteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $carreras = Carrera::all();

        $estudiantes = User::factory()
            ->count(50)
            ->create();

        $estudiantes->each(function ($estudiante) use ($carreras) {
            $carrera = $carreras->random();
            $estudiante->carreras()->sync($carrera->id);
        });

    }
}
