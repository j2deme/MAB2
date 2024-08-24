<?php

use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome');

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

    // MARK: Grupos
    Route::get('/grupos', App\Livewire\Grupos\Index::class)->name('grupos.index');
    Route::get('/grupos/create', App\Livewire\Grupos\Create::class)->name('grupos.create');
    Route::get('/grupos/show/{grupo}', App\Livewire\Grupos\Show::class)->name('grupos.show');
    Route::get('/grupos/update/{grupo}', App\Livewire\Grupos\Edit::class)->name('grupos.edit');

    // MARK: Solicitudes
    Route::get('/solicitudes', \App\Livewire\Movimientos\Index::class)->name('movimientos.index');
    Route::get('/solicitudes/create', \App\Livewire\Movimientos\Create::class)->name('movimientos.create');
    Route::get('/solicitudes/create/{tipo}', \App\Livewire\Movimientos\Create::class)->name('movimientos.request');
    Route::get('/solicitudes/show/{movimiento}', \App\Livewire\Movimientos\Show::class)->name('movimientos.show');
    Route::get('/solicitudes/update/{movimiento}', \App\Livewire\Movimientos\Edit::class)->name('movimientos.edit');

    // MARK: Usuarios
    Route::get('/usuarios', \App\Livewire\Users\Index::class)->name('users.index');
    Route::get('/usuarios/create', \App\Livewire\Users\Create::class)->name('users.create');
    Route::get('/usuarios/show/{user}', \App\Livewire\Users\Show::class)->name('users.show');
    Route::get('/usuarios/update/{user}', \App\Livewire\Users\Edit::class)->name('users.edit');
});



require __DIR__ . '/auth.php';
