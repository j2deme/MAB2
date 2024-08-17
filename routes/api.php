<?php

use App\Models\Carrera;
use App\Models\Materia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');

Route::name('api.')->group(function () {
    Route::get('/carreras', function (Request $request) {
        return Carrera::all('id', 'nombre')->toJson();
    })->name('carreras.index');

    Route::get('/materias', function (Request $request) {
        $materias = Materia::all('id', 'nombre_completo', 'clave');
        $materias->each(function ($materia) {
            $materia->nombre_visual = "{$materia->clave} - {$materia->nombre_completo}";
        });
        return $materias->toJson();
    })->name('materias.index');
});

