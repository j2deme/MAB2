<?php

use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

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
});



require __DIR__ . '/auth.php';
