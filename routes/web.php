<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MateriasController;
use App\Http\Controllers\GruposController;
use App\Http\Controllers\UserController;
use Livewire\Volt\Volt;

// Route::view('/', 'welcome');
Volt::route('/', 'pages.auth.login');

Route::view('/playground', 'playground')->name('playground');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

Route::middleware(['auth'])->group(function () {
    // MARK: Semestres
    Route::get('/semestres', App\Livewire\Semestres\Index::class)->name('semestres.index');
    Route::get('/semestres/create', App\Livewire\Semestres\Create::class)->name('semestres.create');
    Route::get('/semestres/show/{semestre}', App\Livewire\Semestres\Show::class)->name('semestres.show');
    Route::get('/semestres/update/{semestre}', App\Livewire\Semestres\Edit::class)->name('semestres.edit');

    // MARK: Carreras
    Route::get('/carreras', App\Livewire\Carreras\Index::class)->name('carreras.index');
    Route::get('/carreras/create', App\Livewire\Carreras\Create::class)->name('carreras.create');
    Route::get('/carreras/show/{carrera}', App\Livewire\Carreras\Show::class)->name('carreras.show');
    Route::get('/carreras/update/{carrera}', App\Livewire\Carreras\Edit::class)->name('carreras.edit');

    // MARK: Materias
    Route::get('/materias', App\Livewire\Materias\Index::class)->name('materias.index');
    Route::get('/materias/create', App\Livewire\Materias\Create::class)->name('materias.create');
    Route::get('/materias/show/{materia}', App\Livewire\Materias\Show::class)->name('materias.show');
    Route::get('/materias/update/{materia}', App\Livewire\Materias\Edit::class)->name('materias.edit');
    Route::get('/materias/upload', [MateriasController::class, 'batch'])->name('materias.batch');
    Route::post('/materias/upload', [MateriasController::class, 'upload'])->name('materias.upload');

    // MARK: Grupos
    Route::get('/grupos', App\Livewire\Grupos\Index::class)->name('grupos.index');
    Route::get('/grupos/create', App\Livewire\Grupos\Create::class)->name('grupos.create');
    Route::get('/grupos/show/{grupo}', App\Livewire\Grupos\Show::class)->name('grupos.show');
    Route::get('/grupos/update/{grupo}', App\Livewire\Grupos\Edit::class)->name('grupos.edit');
    Route::get('/grupos/upload', [GruposController::class, 'batch'])->name('grupos.batch');
    Route::post('/grupos/upload', [GruposController::class, 'upload'])->name('grupos.upload');

    // MARK: Solicitudes
    Route::get('/solicitudes', \App\Livewire\Movimientos\Index::class)->name('movimientos.index');
    Route::get('/solicitudes/create', \App\Livewire\Movimientos\Create::class)->name('movimientos.create');
    Route::get('/solicitudes/create/{tipo}', \App\Livewire\Movimientos\Create::class)->name('movimientos.request');
    Route::get('/solicitudes/show/{movimiento}', \App\Livewire\Movimientos\Show::class)->name('movimientos.show');
    Route::get('/solicitudes/update/{movimiento}', \App\Livewire\Movimientos\Edit::class)->name('movimientos.edit');
    Route::get('/solicitudes/pendientes', \App\Livewire\Movimientos\Index::class)->name('movimientos.pending');
    Route::get('/solicitudes/atendidas', \App\Livewire\Movimientos\Index::class)->name('movimientos.attended');
    Route::get('/solicitudes/materias', \App\Livewire\Movimientos\ListaMaterias::class)->name('movimientos.materias');
    Route::get('/solicitudes/materias/{clave}', \App\Livewire\Movimientos\Index::class)->name('movimientos.materias.clave');
    Route::get('/solicitudes/generacion', \App\Livewire\Movimientos\ListaGeneracion::class)->name('movimientos.generacion');
    Route::get('/solicitudes/generacion/{estudiante}', \App\Livewire\Movimientos\Index::class)->name('movimientos.generacion.estudiante');

    // MARK: Usuarios
    Route::get('/usuarios', \App\Livewire\Users\Index::class)->name('users.index');
    Route::get('/usuarios/create', \App\Livewire\Users\Create::class)->name('users.create');
    Route::get('/usuarios/show/{user}', \App\Livewire\Users\Show::class)->name('users.show');
    Route::get('/usuarios/update/{user}', \App\Livewire\Users\Edit::class)->name('users.edit');
    Route::get('/usuarios/upload', [UserController::class, 'batch'])->name('users.batch');
    Route::post('/usuarios/upload', [UserController::class, 'upload'])->name('users.upload');

    // MARK: Eventos
    Route::get('/eventos', \App\Livewire\Eventos\Index::class)->name('eventos.index');
    Route::get('/eventos/create', \App\Livewire\Eventos\Create::class)->name('eventos.create');
    Route::get('/eventos/show/{evento}', \App\Livewire\Eventos\Show::class)->name('eventos.show');
    Route::get('/eventos/update/{evento}', \App\Livewire\Eventos\Edit::class)->name('eventos.edit');

    // MARK: Actividades
    Route::get('/actividades', \App\Livewire\Actividades\Index::class)->name('actividades.index');
    Route::get('/actividades/create', \App\Livewire\Actividades\Create::class)->name('actividades.create');
    Route::get('/actividades/show/{actividad}', \App\Livewire\Actividades\Show::class)->name('actividades.show');
    Route::get('/actividades/update/{actividad}', \App\Livewire\Actividades\Edit::class)->name('actividades.edit');

    // MARK: Asistencias
    Route::get('/asistencias', \App\Livewire\Asistencias\Index::class)->name('asistencias.index');
    Route::get('/asistencias/create', \App\Livewire\Asistencias\Create::class)->name('asistencias.create');
    Route::get('/asistencias/show/{asistencia}', \App\Livewire\Asistencias\Show::class)->name('asistencias.show');
    Route::get('/asistencias/update/{asistencia}', \App\Livewire\Asistencias\Edit::class)->name('asistencias.edit');
});

Route::get('/asistencias/magistral', \App\Livewire\Asistencias\Magistral::class)->name('asistencias.magistral');



require __DIR__ . '/auth.php';
