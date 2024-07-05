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
    Route::get('/semestres', \App\Livewire\Semestres\Index::class)->name('semestres.index');
    Route::get('/semestres/create', \App\Livewire\Semestres\Create::class)->name('semestres.create');
    Route::get('/semestres/show/{semestre}', \App\Livewire\Semestres\Show::class)->name('semestres.show');
    Route::get('/semestres/update/{semestre}', \App\Livewire\Semestres\Edit::class)->name('semestres.edit');
});

require __DIR__ . '/auth.php';
