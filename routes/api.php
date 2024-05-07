<?php

use App\Http\Controllers\Api\AdminController;
use App\Http\Controllers\Api\Authentication\BasicAuthController;
use App\Http\Controllers\Api\JwtAuthentication\JwtAuthController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\ProgramController;
use App\Http\Controllers\Api\ProgramTypeController;
use App\Http\Controllers\Api\SalesmanController;
use App\Http\Controllers\Api\StoreInfoDistriController;
use App\Http\Middleware\JwtAuthMiddleware;
use Illuminate\Support\Facades\Route;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');

// rest api version 1
Route::group([
    'prefix' => 'v1',
    'middleware' => 'GrahamCampbell\Throttle\Http\Middleware\ThrottleMiddleware:500,60',
], function () {
    Route::group([
        'prefix' => 'auth'
    ], function () {
        Route::post('/login', [BasicAuthController::class, 'login']);
        Route::post('/logout', [BasicAuthController::class, 'logout']);
        Route::patch('/reset-password', [BasicAuthController::class, 'resetPassword']);
    });

    // for create new salesman account.
    Route::post('/salesmen', [SalesmanController::class, 'storeOneData']);

    // for create new admin account.
    Route::post('/admins', [AdminController::class, 'storeOneData']);

    // salesman's routes
    Route::get('/salesmen', [SalesmanController::class, 'getAll']);
    Route::get('/salesmen/{id}', [SalesmanController::class, 'getOne']);
    Route::post('/salesmen/{number}/visits', [SalesmanController::class, 'checkInVisit']);
    Route::put('/salesmen/{number}/visits/{visitId}', [SalesmanController::class, 'checkOutVisit']);
    Route::put('/salesmen/{number}', [SalesmanController::class, 'updateOne']);
    Route::patch('/salesmen/{number}/profiles', [SalesmanController::class, 'updateProfile']);
    Route::patch('/salesmen/{number}/change-password', [SalesmanController::class, 'changePassword']);
    Route::delete('/salesmen/{number}', [SalesmanController::class, 'removeOne']);
    Route::get('/salesmen/{id}/visits', [SalesmanController::class, 'getVisits']);
    Route::get('/salesmen/{id}/visits/{visitId}', [SalesmanController::class, 'getOneVisit']);
    Route::get('/salesmen/{number}/call-plans', [SalesmanController::class, 'getCallPlans']);
    Route::get('/salesmen/{number}/call-plans/{callPlanId}', [SalesmanController::class, 'getOneCallPlan']);

    // program type's routes.
    Route::get('/program-types', [ProgramTypeController::class, 'getAllData']);
    Route::get('/program-types/{id}', [ProgramTypeController::class, 'getOneData']);
    Route::post('/program-types', [ProgramTypeController::class, 'storeNewProgramType']);
    Route::put('/program-types/{id}', [ProgramTypeController::class, 'updateRecentProgramType']);
    Route::delete('/program-types/{id}', [ProgramTypeController::class, 'removeRecentProgramType']);

    // program's routes.
    Route::get('/programs', [ProgramController::class, 'getAll']);
    Route::get('/programs/{id}', [ProgramController::class, 'getOne']);
    Route::get('/programs/filter', [ProgramController::class, 'getAllDataByDateRangeFilter']);
    Route::post('/programs', [ProgramController::class, 'storeOne']);
    Route::put('/programs/{id}', [ProgramController::class, 'updateOne']);
    Route::delete('/programs/{id}', [ProgramController::class, 'removeOne']);
    Route::get('/programs/{id}/types', [ProgramController::class, 'getProgramType']);
    Route::get('/programs/{id}/details', [ProgramController::class, 'getProgramDetails']);
    Route::get('/programs/{id}/details/{detailId}', [ProgramController::class, 'getOneProgramDetail']);

    // product's routes.
    Route::get('/products', [ProductController::class, 'getAll']);
    Route::get('/products/{number}', [ProductController::class, 'getOne']);
    Route::post('/products', [ProductController::class, 'storeOne']);
    Route::put('/products/{number}', [ProductController::class, 'updateOne']);
    Route::delete('/programs/{number}', [ProductController::class, 'removeOne']);

    // store's routes.
    Route::get('/stores', [StoreInfoDistriController::class, 'getAll']);        
    Route::get('/stores/{id}', [StoreInfoDistriController::class, 'getOne']);
    Route::get('/stores/{id}/owners', [StoreInfoDistriController::class, 'getAllOwners']);
    Route::get('/stores/{id}/owners/{ownerId}', [StoreInfoDistriController::class, 'getOneOwner']);
    Route::get('/stores/{id}/visits', [StoreInfoDistriController::class, 'getAllVisits']);
    Route::get('/stores/{id}/visits/{visitId}', [StoreInfoDistriController::class, 'getOneVisit']);
    Route::get('/stores/{id}/orders', [StoreInfoDistriController::class, 'getAllOrders']);
    Route::get('/stores/{id}/orders/{orderId}', [StoreInfoDistriController::class, 'getOneOrder']);
    Route::get('/stores/filter', [StoreInfoDistriController::class, 'getAllDataByOrderDateFilter']);
});

// rest api version 2
Route::group([
    'prefix' => 'v2',
    'middleware' => 'GrahamCampbell\Throttle\Http\Middleware\ThrottleMiddleware:500,60',
], function () {
    Route::group([
        'prefix' => 'auth',
    ], function () {
        Route::post('/login', [JwtAuthController::class, 'login']);
        Route::post('/refresh', [JwtAuthController::class, 'refreshToken']);
        Route::group([
            'middleware' => JwtAuthMiddleware::class
        ], function () {
            Route::get('/me', [JwtAuthController::class, 'checkSelf']);
        });
    });

    // for create new salesman account.
    Route::post('/salesmen', [SalesmanController::class, 'storeOneData']);

    // for create new admin account.
    Route::post('/admins', [AdminController::class, 'storeOneData']);

    Route::group([
        'middleware' => JwtAuthMiddleware::class,
    ], function () {
        // salesman's routes
        Route::get('/salesmen', [SalesmanController::class, 'getAll']);
        Route::get('/salesmen/{id}', [SalesmanController::class, 'getOne']);
        Route::post('/salesmen/{number}/visits', [SalesmanController::class, 'uploadFile']);
        // Route::post('/salesmen/{number}/visits', [SalesmanController::class, 'checkInVisit']);
        Route::post('/salesmen/{number}/visits/{visitId}', [SalesmanController::class, 'checkOutVisit']);
        Route::put('/salesmen/{number}', [SalesmanController::class, 'updateOne']);
        Route::patch('/salesmen/{number}/profiles', [SalesmanController::class, 'updateProfile']);
        Route::patch('/salesmen/{number}/change-password', [SalesmanController::class, 'changePassword']);
        Route::delete('/salesmen/{number}', [SalesmanController::class, 'removeOne']);
        Route::get('/salesmen/{id}/visits', [SalesmanController::class, 'getVisits']);
        Route::get('/salesmen/{id}/visits/{visitId}', [SalesmanController::class, 'getOneVisit']);
        Route::get('/salesmen/{number}/call-plans', [SalesmanController::class, 'getCallPlans']);
        Route::get('/salesmen/{number}/call-plans/{callPlanId}', [SalesmanController::class, 'getOneCallPlan']);

        // program type's routes.
        Route::get('/program-types', [ProgramTypeController::class, 'getAllData']);
        Route::get('/program-types/{id}', [ProgramTypeController::class, 'getOneData']);
        Route::post('/program-types', [ProgramTypeController::class, 'storeNewProgramType']);
        Route::put('/program-types/{id}', [ProgramTypeController::class, 'updateRecentProgramType']);
        Route::delete('/program-types/{id}', [ProgramTypeController::class, 'removeRecentProgramType']);

        // program's routes.
        Route::get('/programs', [ProgramController::class, 'getAll']);
        Route::get('/programs/{id}', [ProgramController::class, 'getOne']);
        Route::get('/programs/filter', [ProgramController::class, 'getAllDataByPeriodeFilter']);
        Route::post('/programs', [ProgramController::class, 'storeOne']);
        Route::put('/programs/{id}', [ProgramController::class, 'updateOne']);
        Route::delete('/programs/{id}', [ProgramController::class, 'removeOne']);
        Route::get('/programs/{id}/types', [ProgramController::class, 'getProgramType']);
        Route::get('/programs/{id}/details', [ProgramController::class, 'getProgramDetails']);
        Route::get('/programs/{id}/details/{detailId}', [ProgramController::class, 'getOneProgramDetail']);

        // product's routes.
        Route::get('/products', [ProductController::class, 'getAll']);
        Route::get('/products/{number}', [ProductController::class, 'getOne']);
        Route::post('/products', [ProductController::class, 'storeOne']);
        Route::put('/products/{number}', [ProductController::class, 'updateOne']);
        Route::delete('/programs/{number}', [ProductController::class, 'removeOne']);

        // store's routes.
        Route::get('/stores', [StoreInfoDistriController::class, 'getAll']);        
        Route::get('/stores/{id}', [StoreInfoDistriController::class, 'getOne']);
        Route::get('/stores/{id}/owners', [StoreInfoDistriController::class, 'getAllOwners']);
        Route::get('/stores/{id}/owners/{ownerId}', [StoreInfoDistriController::class, 'getOneOwner']);
        Route::get('/stores/{id}/visits', [StoreInfoDistriController::class, 'getAllVisits']);
        Route::get('/stores/{id}/visits/{visitId}', [StoreInfoDistriController::class, 'getOneVisit']);
        Route::get('/stores/{id}/orders', [StoreInfoDistriController::class, 'getAllOrders']);
        Route::get('/stores/{id}/orders/{orderId}', [StoreInfoDistriController::class, 'getOneOrder']);
        Route::get('/stores/filter', [StoreInfoDistriController::class, 'getAllDataByOrderDateFilter']);
        Route::get('/stores/filter', [StoreInfoDistriController::class, 'getAllDataByOrderDateFilter']);
    });
});
