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
            ->when($request->exists('selected'), fn(Builder $query) => $query->whereIn('id', $request->input('selected', [])))
            ->unless($request->filled('search') || $request->filled('carrera_id') || $request->exists('selected'), fn(Builder $query) => $query->limit(10))
            ->orderBy('nombre')
            ->get();
    })->name('carreras.index');

    Route::get('/materias', function (Request $request) {
        $request->headers->set('Content-Type', 'application/json');

        return Materia::query()
            ->with('carrera')
            ->select('id', 'clave', 'nombre_completo', 'carrera_id')
            ->when($request->filled('carrera_id'), fn(Builder $query) => $query->where('carrera_id', $request->integer('carrera_id')))
            // Some clients (WireUI async selects) may discard querystring params
            // when performing subsequent fetches (search/pagination). To avoid
            // returning unsafely unfiltered materias when a carrera is selected
            // but no explicit `available` flag is present, we treat the request
            // as requesting only available materias by default when called
            // for a specific `carrera_id` without a search term or selected set.
            ->when(function () use ($request) {
                return $request->boolean('available')
                    // Treat as available when a carrera is selected and the client
                    // is requesting the default list (no search, no selected items).
                    // Use `filled('selected')` instead of `exists` because some
                    // clients send an empty `selected` param which would make
                    // `exists` true and skip the available filter.
                    || ($request->filled('carrera_id') && !$request->filled('search') && !$request->filled('selected'));
            }, function (Builder $query) {
                $semestre = \Cache::remember('semestre_activo', 3600, function () {
                    return Semestre::where('activo', true)->first();
                });

                $query->whereHas('grupos', fn(Builder $q) => $q
                    ->where('semestre_id', $semestre?->id)
                    ->where('is_disponible', true));
            })
            ->when(
                $request->search,
                fn(Builder $query) => $query
                    ->where(fn(Builder $searchQuery) => $searchQuery
                        ->where('clave', 'like', "%{$request->search}%")
                        ->orWhere('nombre_completo', 'like', "%{$request->search}%"))
            )
            ->when($request->exists('selected'), fn(Builder $query) => $query->whereIn('id', $request->input('selected', [])))
            ->orderBy('clave')
            ->get()
            ->map(function (Materia $materia) {
                $siglas                 = optional($materia->carrera)->siglas ?? '';
                $materia->nombre_visual = "{$siglas} - {$materia->nombre_completo} ({$materia->clave})";

                return $materia;
            });
    })->name('materias.index');

    Route::get('/grupos', function (Request $request) {
        $request->headers->set('Content-Type', 'application/json');

        $semestre = \Cache::remember('semestre_activo', 3600, function () {
            return Semestre::where('activo', true)->first();
        });

        // If there is no active semester, return an empty collection early to
        // avoid queries that assume a semester ID (which would throw an error).
        if (!$semestre) {
            return collect();
        }

        $query = Grupo::query()
            ->where('semestre_id', $semestre->id)
            ->with([
                'materia' => function ($q) {
                    $q->select('id', 'clave', 'nombre_completo', 'carrera_id')
                        ->with(['carrera' => fn($c) => $c->select('id', 'nombre', 'siglas')]);
                }
            ])
            ->select('id', 'siglas', 'materia_id');

        if ($request->filled('carrera_id')) {
            $query->whereHas('materia', fn(Builder $q) => $q->where('carrera_id', $request->integer('carrera_id')));
        }

        if ($request->filled('materia_id')) {
            $query->where('materia_id', $request->integer('materia_id'));
        }

        if ($request->boolean('available')) {
            $query->where('is_disponible', true);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('materia', function (Builder $q) use ($search) {
                $q->where(fn(Builder $searchQuery) => $searchQuery
                    ->where('clave', 'like', "%{$search}%")
                    ->orWhere('nombre_completo', 'like', "%{$search}%"));
            });
        }

        if ($request->exists('selected')) {
            $query->whereIn('id', $request->input('selected', []));
        }

        $grupos = $query->orderBy('id')->get()->map(function (Grupo $grupo) {
            // Compose a human-friendly name used as option-label in selects
            $materia        = $grupo->materia;
            $clave          = optional($materia)->clave ?? '';
            $nombreCompleto = optional($materia)->nombre_completo ?? '';
            $siglas         = $grupo->siglas ?? '';

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

