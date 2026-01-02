<?php

use App\Http\Controllers\Api\Auth\AuthController;
use Illuminate\Support\Facades\Route;

Route::prefix('auth')->group(function () {
    Route::post('/login', [AuthController::class, 'login'])
        ->name('auth.login');
    Route::post('/register', [AuthController::class, 'register'])
        ->name('auth.register');
    Route::post('/refresh', [AuthController::class, 'refresh'])
        ->name('auth.refresh');
    Route::post('/logout', [AuthController::class, 'logout'])
        ->middleware('auth:api')
        ->name('auth.logout');
    Route::get('/me', [AuthController::class, 'me'])
        ->middleware('auth:api')
        ->name('auth.me');
});