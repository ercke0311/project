<?php

use App\Http\Controllers\Api\Auth\AuthController;
use App\Http\Controllers\Api\Auth\SocialLoginController;
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
    //third party
    Route::get('/social/{driver}/url', [SocialLoginController::class, 'getRedirectUrl'])
        ->name('auth.social.url');
    Route::get('/social/{driver}/callback', [SocialLoginController::class, 'callback'])
        ->name('auth.social.callback');
});