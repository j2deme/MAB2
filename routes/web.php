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
    Volt::route('/semestres', 'semestres.index')->name('semestres.index');
    Volt::route('/semestres/create', 'semestres.create')->name('semestres.create');
    Volt::route('/semestres/show/{semestre}', 'semestres.show')->name('semestres.show');
    Volt::route('/semestres/update/{semestre}', 'semestres.edit')->name('semestres.edit');
    Volt::route('/semestres/delete/{semestre}', 'semestres.delete')->name('semestres.delete');
});



require __DIR__ . '/auth.php';
