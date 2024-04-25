<?php

use App\Http\Controllers\Api\Authentication\BasicAuthController;
use Illuminate\Support\Facades\Route;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');

Route::group(['prefix' => 'v1'], function () {
    Route::group(['prefix' => 'auth'], function () {
        Route::post('/login', [BasicAuthController::class, 'login']);
        Route::post('/register', [BasicAuthController::class, 'register']);
        Route::post('/logout', [BasicAuthController::class, 'logout']);
    });
});
