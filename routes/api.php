<?php

use App\Modules\Auth\Controllers\AuthController;
use App\Modules\Project\Controllers\Admin\ProjectController;
use App\Modules\Project\Controllers\Public\PublicProjectController;
use App\Modules\User\Controllers\Admin\ExperienceController;
use App\Modules\User\Controllers\Admin\HobbyController;
use App\Modules\User\Controllers\Admin\SkillController;
use App\Modules\User\Controllers\Admin\UserContactController;
use App\Modules\User\Controllers\Admin\UserController;
use App\Modules\User\Controllers\Public\PublicExperienceController;
use App\Modules\UserProfile\Controllers\Admin\UserProfileController;
use App\Modules\UserProfile\Controllers\Public\PublicProfileController;
use Illuminate\Support\Facades\Route;

Route::post('/login', [AuthController::class, 'login']);

Route::middleware(['auth:sanctum'])->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'user']);

    Route::middleware(['role:super-admin'])->group(function () {
        Route::apiResource('users', UserController::class);
        Route::get('/users/{user}/profile', [UserProfileController::class, 'show']);
        Route::post('/users/{user}/profile', [UserProfileController::class, 'store']);
        Route::get('/users/{user}/profile/cv', [UserProfileController::class, 'streamCv'])->name('users.profile.cv');

        Route::get('/hobbies', [HobbyController::class, 'index']);
        Route::get('/skills', [SkillController::class, 'index']);

        Route::get('/users/{user}/experiences', [ExperienceController::class, 'index']);
        Route::post('/users/{user}/experiences', [ExperienceController::class, 'store']);
        Route::put('/experiences/{experience}', [ExperienceController::class, 'update']);
        Route::delete('/experiences/{experience}', [ExperienceController::class, 'destroy']);

        Route::get('/users/{user}/contacts', [UserContactController::class, 'index']);
        Route::post('/users/{user}/contacts', [UserContactController::class, 'store']);
        Route::put('/contacts/{contact}', [UserContactController::class, 'update']);
        Route::delete('/contacts/{contact}', [UserContactController::class, 'destroy']);

        // Admin project routes
        Route::get('/users/{user}/projects', [ProjectController::class, 'index']);
        Route::post('/users/{user}/projects', [ProjectController::class, 'store']);
        Route::get('/projects/{project}', [ProjectController::class, 'show']);
        Route::post('/projects/{project}', [ProjectController::class, 'update']);
        Route::delete('/projects/{project}', [ProjectController::class, 'destroy']);
    });
});

Route::prefix('public')->group(function () {
    // Home (Profile + Preview Exp/Proj)
    Route::get('/home', [PublicProfileController::class, 'getHome']);

    // Assets Stream
    Route::get('/profile/photo/{id}', [PublicProfileController::class, 'streamPhoto'])->name('public.profile.photo');
    Route::get('/profile/cv/{id}', [PublicProfileController::class, 'streamCv'])->name('public.profile.cv');
    Route::get('/project/thumbnail/{id}', [PublicProfileController::class, 'streamProjectThumbnail'])->name('public.project.thumbnail');

    // Dedicated Lists
    Route::get('/experiences', [PublicExperienceController::class, 'index']);
    Route::get('/projects', [PublicProjectController::class, 'index']);
    Route::get('/projects/{slug}', [PublicProjectController::class, 'show']);
});
