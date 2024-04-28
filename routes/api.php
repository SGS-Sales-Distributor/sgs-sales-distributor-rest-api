<?php

use App\Http\Controllers\Api\Authentication\BasicAuthController;
use App\Http\Controllers\Api\MasterCallPlanController;
use App\Http\Controllers\Api\MasterStatusController;
use App\Http\Controllers\Api\MasterTargetNooController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\ProgramController;
use App\Http\Controllers\Api\StoreInfoDistriController;
use Illuminate\Support\Facades\Route;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');

Route::group([
    'prefix' => 'v1',
    'middleware' => 'GrahamCampbell\Throttle\Http\Middleware\ThrottleMiddleware:500,60',
    ], function () {
    Route::group(['prefix' => 'auth'], function () {
        Route::post('/login', [BasicAuthController::class, 'login']);
        Route::post('/register', [BasicAuthController::class, 'register']);
        Route::post('/logout', [BasicAuthController::class, 'logout']);
        Route::put('/reset-password', [BasicAuthController::class, 'resetPassword']);
    });

    // users routes.


    // master call plan routes.
    Route::get('/master-call-plans', [MasterCallPlanController::class, 'getAllData']);
    Route::get('/master-call-plans/search', [MasterCallPlanController::class, 'getAllDataByQuery']);
    Route::get('/master-call-plans/filter', [MasterCallPlanController::class, 'getAllDataByDateFilter']);
    Route::get('/master-call-plans/{id}', [MasterCallPlanController::class, 'getOneData']);
    // Route::post('/master-call-plan', [MasterCallPlanController::class, 'storeOneData']);
    // Route::put('/master-call-plan/{id}', [MasterCallPlanController::class, 'updateOneData']);
    // Route::delete('/master-call-plan/{id}', [MasterCallPlanController::class, 'removeOneData']);
    
    // master target noo routes.
    Route::get('/master-target-noos', [MasterTargetNooController::class, 'getAllData']);
    Route::get('/master-target-noos/search', [MasterTargetNooController::class, 'getAllDataByQuery']);
    Route::get('/master-target-noos/filter', [MasterTargetNooController::class, 'getAllDataByYearFilter']);
    Route::get('/master-target-noos/{id}', [MasterTargetNooController::class, 'getOneData']);

    // programs routes.
    Route::get('/programs', [ProgramController::class, 'getAllData']);
    Route::get('/programs/search', [ProgramController::class, 'getAllDataByQuery']);
    Route::get('/programs/filter', [ProgramController::class, 'getAllDataByPeriodeFilter']);
    Route::get('/programs/{id}', [ProgramController::class, 'getOneData']);

    // master status routes.
    Route::get('master-statuses', [MasterStatusController::class, 'getAllData']);
    Route::get('master-statuses/search', [MasterStatusController::class, 'getAllDataByQuery']);
    Route::get('master-statuses/filter', [MasterStatusController::class, 'getAllDataByOrderDateFilter']);
    Route::get('master-statuses/{id}', [MasterStatusController::class, 'getOneData']);

    // stores routes.
    Route::get('/stores', [StoreInfoDistriController::class, 'getAllData']);
    Route::get('/stores/search', [StoreInfoDistriController::class, 'getAllDataByQuery']);
    Route::get('/stores/filter', [StoreInfoDistriController::class, 'getAllDataByOrderDateFilter']);
    Route::get('/stores/{id}', [StoreInfoDistriController::class, 'getOneData']);

    // orders routes.

    // products routes.
    Route::get('/products', [ProductController::class, 'getAllData']);
    Route::get('/products/search', [ProductController::class, 'getAllDataByQuery']);
    Route::get('/products/{id}', [ProductController::class, 'getOneData']);

    // brands routes.

    // returs routes.
});
