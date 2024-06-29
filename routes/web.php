<?php

use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;

// Rutas no autenticadas
Route::get('/', [DashboardController::class, 'index']);

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    // Rutas autenticadas
    Route::get('/dashboard', [DashboardController::class, 'dashboard'])->name('dashboard');
    Route::resource('/categories', \App\Http\Controllers\CategoryController::class);
    Route::resource('/lessons', \App\Http\Controllers\LessonController::class);
    Route::resource('/roles', \App\Http\Controllers\RoleController::class);
});
