<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PontoController;
use Illuminate\Support\Facades\Route;

Route::get('/ponto', [PontoController::class, 'index'])
    ->middleware('auth')
    ->name('ponto');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware('auth')->group(function () {
    Route::get('/', function () {
        return redirect('/ponto');
    });
    Route::post('/entrada', [PontoController::class, 'entrada']);
    Route::get('/saida/{id}', [PontoController::class, 'saida']);
});

require __DIR__ . '/auth.php';
