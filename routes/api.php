<?php

use App\Modules\User\Controllers\UserController;
use App\Modules\UserProfile\Controllers\PublicProfileController;
use App\Modules\UserProfile\Controllers\UserProfileController;
use App\Modules\User\Controllers\HobbyController;
use App\Modules\User\Controllers\SkillController;
use Illuminate\Support\Facades\Route;
use App\Modules\Auth\Controllers\AuthController;

Route::post('/login', [AuthController::class, 'login']);

Route::middleware(['auth:sanctum'])->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'user']);
    Route::middleware(['role:super-admin'])->group(function () {
        Route::apiResource('users', UserController::class);
        Route::get('/users/{user}/profile', [UserProfileController::class, 'show']);
        Route::post('/users/{user}/profile', [UserProfileController::class, 'store']);
        Route::get('/users/{user}/profile/cv', [UserProfileController::class, 'downloadCv'])->name('users.profile.cv');

        Route::get('/hobbies', [HobbyController::class, 'index']);

        Route::get('/skills', [SkillController::class, 'index']);
    });
});

Route::prefix('public/profile')->group(function () {
    Route::get('/active', [PublicProfileController::class, 'getActive']);
    Route::get('/photo/{id}', [PublicProfileController::class, 'streamPhoto'])->name('public.profile.photo');
    Route::get('/cv/{id}', [PublicProfileController::class, 'downloadCv'])->name('public.profile.cv');
});
