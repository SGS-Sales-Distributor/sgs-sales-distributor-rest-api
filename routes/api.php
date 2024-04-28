<?php

use App\Http\Controllers\Api\Authentication\BasicAuthController;
use App\Http\Controllers\Api\MasterCallPlanController;
use App\Http\Controllers\Api\MasterStatusController;
use App\Http\Controllers\Api\MasterTargetNooController;
use App\Http\Controllers\Api\MasterTypeProgramController;
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

    // master type program routes.
    Route::get('/master-type-programs', [MasterTypeProgramController::class, 'getAllData']);
    Route::get('/master-type-programs/search', [MasterTypeProgramController::class, 'getAllDataByQuery']);
    Route::get('/master-type-programs/filter', [MasterTypeProgramController::class, 'getAllDataByPeriodeFilter']);
    Route::get('/master-type-programs/{id}', [MasterTypeProgramController::class, 'getOneData']);

    // master status routes.
    Route::get('master-statuses', [MasterStatusController::class, 'getAllData']);
    Route::get('master-statuses/search', [MasterStatusController::class, 'getAllDataByQuery']);
    Route::get('master-statuses/filter', [MasterStatusController::class, 'getAllDataByOrderDateFilter']);
    Route::get('master-statuses/{id}', [MasterStatusController::class, 'getOneData']);

    // store info distri routes.
    Route::get('/store-info-distris', [StoreInfoDistriController::class, 'getAllData']);
    Route::get('/store-info-distris/search', [StoreInfoDistriController::class, 'getAllDataByQuery']);
    Route::get('/store-info-distris/filter', [StoreInfoDistriController::class, 'getAllDataByOrderDateFilter']);
    Route::get('/store-info-distris/{id}', [StoreInfoDistriController::class, 'getOneData']);
});
