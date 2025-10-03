<?php

use App\Models\Carrera;
use App\Models\Materia;
use App\Models\Grupo;
use App\Models\Semestre;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Hash;
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

    Route::get('/estudiantes', function (Request $request) {
        $request->headers->set('Content-Type', 'application/json');

        return User::query()
            ->where('rol', \App\Enums\UserRoles::ESTUDIANTE)
            ->select('id', 'name', 'username')
            ->when(
                $request->search,
                fn(Builder $query) => $query
                    ->where('username', 'like', "%{$request->search}%")
            )
            ->when(
                $request->exists('selected'),
                fn(Builder $query) => $query->whereIn('id', $request->input('selected', [])),
                fn(Builder $query) => $query->limit(10)
            )
            ->orderBy('username')
            ->get();
    })->name('estudiantes.index');

    // Ruta unificada para validar/obtener datos del estudiante (GET o POST)
    Route::match(['get', 'post'], '/validate/student', function (Request $request) {
        try {
            $request->headers->set('Content-Type', 'application/json');

            $isPost = $request->isMethod('post');

            // Validación condicional
            $rules = ['username' => 'required|string'];
            if ($isPost) {
                $rules['password'] = 'required|string';
            }
            $validated = $request->validate($rules);

            // Buscar estudiante
            $student = User::query()
                ->where('rol', \App\Enums\UserRoles::ESTUDIANTE)
                ->where('username', $validated['username'])
                ->with([
                    'carreras' => function ($query) {
                        $query->select('carreras.id', 'nombre', 'siglas', 'clave_interna');
                    }
                ])
                ->select('id', 'name', 'username', 'email', $isPost ? 'password' : null)
                ->first();

            if (!$student) {
                return response()->json([
                    'success' => false,
                    'error' => $isPost ? 'Credenciales invalidas' : 'No encontrado',
                    'message' => $isPost ? 'No se encontró ningún estudiante con los datos proporcionados.' : 'No se encontró ningún estudiante con el número de control proporcionado.'
                ], $isPost ? 401 : 404);
            }

            // Si es POST, verificar contraseña
            if ($isPost) {
                if (!Hash::check($validated['password'], $student->password)) {
                    return response()->json([
                        'success' => false,
                        'error' => 'Credenciales invalidas',
                        'message' => 'La contraseña proporcionado es incorrecto'
                    ], 401);
                }
            }

            $career = $student->carreras->first();

            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $student->id,
                    'username' => $student->username,
                    'name' => $student->name,
                    'email' => $student->email,
                    'career' => $career ? [
                        'id' => $career->id,
                        'name' => $career->nombre,
                        'siglas' => $career->siglas,
                        'clave_interna' => $career->clave_interna
                    ] : null
                ]
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Error inesperado',
                'message' => 'Se produjo un error al procesar la solicitud.'
            ], 500);
        }
    })->name('estudiantes.validate');

    Route::fallback(function () {
        return response()->json([
            'success' => false,
            'error' => 'Endpoint no encontrado',
            'message' => 'La ruta solicitada no existe o requiere un método diferente.'
        ], 404);
    });
});

