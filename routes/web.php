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
    Volt::route('/semestres', 'semestres.index')->name('semestres.index');
    Volt::route('/semestres/create', 'semestres.create')->name('semestres.create');
    Volt::route('/semestres/show/{semestre}', 'semestres.show')->name('semestres.show');
    Volt::route('/semestres/update/{semestre}', 'semestres.edit')->name('semestres.edit');
    // MARK: Carreras
    Volt::route('/carreras', 'carreras.index')->name('carreras.index');
    Volt::route('/carreras/create', 'carreras.create')->name('carreras.create');
    Volt::route('/carreras/show/{carrera}', 'carreras.show')->name('carreras.show');
    Volt::route('/carreras/update/{carrera}', 'carreras.edit')->name('carreras.edit');
});



require __DIR__ . '/auth.php';
