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

Route::middleware(['auth', 'admin'])->group(function () {
    Route::get('/relatorios', [\App\Http\Controllers\RelatorioAIController::class, 'index'])->name('relatorios.index');
    Route::post('/relatorios/gerar', [\App\Http\Controllers\RelatorioAIController::class, 'gerar'])->name('relatorios.gerar');
});

Route::delete('/ponto/{id}', [PontoController::class, 'destroy'])->name('ponto.destroy');

require __DIR__ . '/auth.php';
