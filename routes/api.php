<?php

use App\Modules\User\Controllers\UserController;
use App\Modules\UserProfile\Controllers\PublicProfileController;
use App\Modules\UserProfile\Controllers\UserProfileController;
use App\Modules\User\Controllers\HobbyController;
use App\Modules\User\Controllers\SkillController;
use App\Modules\User\Controllers\ExperienceController;
use App\Modules\Project\Controllers\ProjectController;
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

        Route::get('/users/{user}/experiences', [ExperienceController::class, 'index']);
        Route::post('/users/{user}/experiences', [ExperienceController::class, 'store']);
        Route::put('/experiences/{experience}', [ExperienceController::class, 'update']);
        Route::delete('/experiences/{experience}', [ExperienceController::class, 'destroy']);

        // Admin project routes
        Route::get('/users/{user}/projects', [ProjectController::class, 'index']);
        Route::post('/users/{user}/projects', [ProjectController::class, 'store']);
        Route::get('/projects/{project}', [ProjectController::class, 'show']);
        Route::post('/projects/{project}', [ProjectController::class, 'update']);
        Route::delete('/projects/{project}', [ProjectController::class, 'destroy']);
    });
});

Route::prefix('public/profile')->group(function () {
    Route::get('/active', [PublicProfileController::class, 'getActive']);
    Route::get('/photo/{id}', [PublicProfileController::class, 'streamPhoto'])->name('public.profile.photo');
    Route::get('/cv/{id}', [PublicProfileController::class, 'downloadCv'])->name('public.profile.cv');
    Route::get('/project/thumbnail/{id}', [PublicProfileController::class, 'streamProjectThumbnail'])
        ->name('public.project.thumbnail');
});
