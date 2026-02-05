<?php

use App\Models\Carrera;
use App\Models\Materia;
use App\Models\Grupo;
use App\Models\Semestre;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Eloquent\Builder;

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

        $semestre = \Cache::remember('semestre_activo', 3600, function () {
            return Semestre::where('activo', true)->first();
        });

        $query = Grupo::query()
            ->where('semestre_id', $semestre->id)
            ->with(['materia' => function ($q) {
                $q->select('id', 'clave', 'nombre_completo', 'carrera_id')
                  ->with(['carrera' => fn($c) => $c->select('id', 'nombre', 'siglas')]);
            }])
            ->select('id', 'siglas', 'materia_id');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('materia', function (Builder $q) use ($search) {
                $q->where('clave', 'like', "%{$search}%")
                  ->orWhere('nombre_completo', 'like', "%{$search}%");
            });
        }

        if ($request->exists('selected')) {
            $query->whereIn('id', $request->input('selected', []));
        } else {
            $query->limit(10);
        }

        $grupos = $query->orderBy('id')->get()->map(function (Grupo $grupo) {
            // Compose a human-friendly name used as option-label in selects
            $materia = $grupo->materia;
            $clave = $materia->clave ?? '';
            $nombreCompleto = $materia->nombre_completo ?? '';
            $siglas = $grupo->siglas ?? '';

            $grupo->setAttribute('nombre', trim(sprintf('%s %s (%s)', $clave, $nombreCompleto, $siglas)));

            // Keep nested materia.carrera so option-description paths like 'materia.carrera.nombre' work
            return $grupo;
        });

        return $grupos;
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
            $select = ['id', 'name', 'username', 'email'];
            if ($isPost) {
                $select[] = 'password';
            }

            $student = User::query()
                ->where('rol', \App\Enums\UserRoles::ESTUDIANTE)
                ->where('username', $validated['username'])
                ->with([
                    'carreras' => function ($query) {
                        $query->select('carreras.id', 'nombre', 'siglas', 'clave_interna');
                    }
                ])
                ->select($select)
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

