<?php

use App\Modules\User\Controllers\UserController;
use Illuminate\Support\Facades\Route;
use App\Modules\Auth\Controllers\AuthController;

Route::post('/login', [AuthController::class, 'login']);

Route::middleware(['auth:sanctum'])->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'user']);
    Route::middleware(['role:super-admin'])->group(function () {
        Route::apiResource('users', UserController::class);
    });
});
