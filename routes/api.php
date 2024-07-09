<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Models\Carrera;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');

Route::name('api.')->group(function () {
    Route::get('/carreras', function (Request $request) {
        return Carrera::all('id', 'nombre')->toJson();
    })->name('carreras.index');
});

