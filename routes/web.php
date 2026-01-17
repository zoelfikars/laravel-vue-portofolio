<?php

use Illuminate\Support\Facades\Route;

// Route::get('/', function () {
//     return response()->json(['message' => 'API Only. Visit laravue-portofolio.local']);
// });
Route::domain('zoelfikars.site')->group(function () {
    Route::get('/{any?}', function () {
        return view('app'); // Memanggil resources/views/app.blade.php
    })->where('any', '.*');
});
Route::domain('api.zoelfikars.site')->group(function () {
    Route::get('/{any?}', function () {
        return response()->json(['message' => 'API Only. Visit zoelfikars.site']);
    })->where('any', '.*');
});

// Opsional: Handle localhost juga (untuk dev/testing local)
Route::get('/{any?}', function () {
    return view('app');
})->where('any', '.*');
