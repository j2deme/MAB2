<?php

use App\Models\Carrera;
use App\Models\Materia;
use App\Models\Grupo;
use App\Models\Semestre;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Contracts\Database\Eloquent\Builder;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');

Route::name('api.')->group(function () {
    Route::get('/carreras', function (Request $request) {
        $request->headers->set('Content-Type', 'application/json');

        return Carrera::query()
            ->select('id', 'nombre', 'siglas')
            ->when(
                $request->search,
                fn(Builder $query) => $query
                    ->orWhere('nombre', 'like', "%{$request->search}%")
                    ->orWhere('siglas', 'like', "%{$request->search}%")
            )
            ->when(
                $request->exists('selected'),
                fn(Builder $query) => $query->whereIn('id', $request->input('selected', [])),
                fn(Builder $query) => $query->limit(10)
            )
            ->orderBy('nombre')
            ->get();
    })->name('carreras.index');

    Route::get('/materias', function (Request $request) {
        $request->headers->set('Content-Type', 'application/json');

        return Materia::query()
            ->with('carrera')
            ->select('id', 'clave', 'nombre_completo', 'carrera_id')
            ->when(
                $request->search,
                fn(Builder $query) => $query
                    ->orWhere('clave', 'like', "%{$request->search}%")
                    ->orWhere('nombre_completo', 'like', "%{$request->search}%")
            )
            ->when(
                $request->exists('selected'),
                fn(Builder $query) => $query->whereIn('id', $request->input('selected', [])),
                fn(Builder $query) => $query->limit(10)
            )
            ->orderBy('clave')
            ->get()
            ->map(function (Materia $materia) {
                $materia->nombre_visual = "{$materia->carrera->siglas} - {$materia->nombre_completo} ({$materia->clave})";

                return $materia;
            });
    })->name('materias.index');

    Route::get('/grupos', function (Request $request) {
        $request->headers->set('Content-Type', 'application/json');

        $semestre = Semestre::where('activo', true)->first();

        return Grupo::query()
            ->where('semestre_id', $semestre->id)
            ->join('materias', 'grupos.materia_id', '=', 'materias.id')
            ->join('carreras', 'materias.carrera_id', '=', 'carreras.id')
            ->select('grupos.id', 'grupos.siglas', 'grupos.materia_id', 'materias.clave', 'materias.nombre_completo', 'materias.carrera_id', 'carreras.nombre as carrera')
            ->when(
                $request->search,
                fn(Builder $query) => $query
                    ->orWhere('clave', 'like', "%{$request->search}%")
                    ->orWhere('nombre_completo', 'like', "%{$request->search}%")
            )
            ->when(
                $request->exists('selected'),
                fn(Builder $query) => $query->whereIn('id', $request->input('selected', [])),
                fn(Builder $query) => $query->limit(10)
            )
            ->orderBy('materias.clave')
            ->get()
            ->map(function (Grupo $grupo) {
                $grupo->nombre_visual = $grupo->nombre;
                $grupo->description   = $grupo->carrera;

                return $grupo;
            });
    })->name('grupos.index');
});

