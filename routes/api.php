<?php

use App\Http\Controllers\Api\Authentication\BasicAuthController;
use App\Http\Controllers\Api\JwtAuthentication\JwtAuthController;
use App\Http\Controllers\Api\MasterCallPlanController;
use App\Http\Controllers\Api\MasterStatusController;
use App\Http\Controllers\Api\MasterTargetNooController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\ProgramController;
use App\Http\Controllers\Api\SalesmanController;
use App\Http\Controllers\Api\StoreInfoDistriController;
use App\Http\Middleware\JwtAuthMiddleware;
use Illuminate\Support\Facades\Route;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');

Route::group([
    'prefix' => 'v1',
    'middleware' => 'GrahamCampbell\Throttle\Http\Middleware\ThrottleMiddleware:500,60',
    ], function () {
    Route::group(['prefix' => 'auth'], function () {
        Route::post('/login', [JwtAuthController::class, 'login']);
        Route::post('/refresh', [JwtAuthController::class, 'refreshToken']);
        Route::group(['middleware' => JwtAuthMiddleware::class], function () {
            Route::get('/me', [JwtAuthController::class, 'checkSelf']);
        });
        Route::group(['prefix' => 'basic'], function () {
            Route::post('/login', [BasicAuthController::class, 'login']);
            Route::post('/register', [BasicAuthController::class, 'register']);
            Route::post('/logout', [BasicAuthController::class, 'logout']);
            Route::put('/reset-password', [BasicAuthController::class, 'resetPassword']);
        });
    });

    // users routes.
    Route::post('/salesmen', [SalesmanController::class, 'storeOneData']);

    Route::group(['middleware' => JwtAuthMiddleware::class], function () {
        // salesman routes.
        Route::get('/salesmen', [SalesmanController::class, 'getAllData']);
        Route::get('/salesmen/{id}/types', [SalesmanController::class, 'getUserTypeData']);
        Route::get('/salesmen/{id}/statuses', [SalesmanController::class, 'getUserStatusData']);
        Route::get('/salesmen/{id}', [SalesmanController::class, 'getOneData']);
        Route::get('/salesmen/{id}/visits', [SalesmanController::class, 'getAllVisitsData']);
        Route::get('/salesmen/{id}/visits/{visitId}', [SalesmanController::class, 'getOneVisit']);
        Route::post('/salesmen/{number}/visits', [SalesmanController::class, 'checkInVisit']);
        Route::put('/salesmen/{number}/visits/{visitId}', [SalesmanController::class, 'checkOutVisit']);

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

        // stores routes.
        Route::get('/stores', [StoreInfoDistriController::class, 'getAllData']);
        Route::get('/stores/{id}', [StoreInfoDistriController::class, 'getOneData']);
        Route::get('/stores/{id}/owners', [StoreInfoDistriController::class, 'getAllOwnersData']);
        Route::get('/stores/{id}/owners/{ownerId}', [StoreInfoDistriController::class, 'getOneOwnerData']);
        Route::get('/stores/{id}/visits', [StoreInfoDistriController::class, 'getAllVisitsData']);
        Route::get('/stores/{id}/visits/{visitId}', [StoreInfoDistriController::class, 'getOneVisitData']);
        Route::get('/stores/{id}/orders', [StoreInfoDistriController::class, 'getAllOrdersData']);
        Route::get('/stores/{id}/orders/{orderId}', [StoreInfoDistriController::class, 'getOneOrderData']);
        Route::get('/stores/search', [StoreInfoDistriController::class, 'getAllDataByQuery']);
        Route::get('/stores/filter', [StoreInfoDistriController::class, 'getAllDataByOrderDateFilter']);
        
        // orders routes.
        Route::get('/orders');
        Route::get('/orders/search');
        Route::get('/orders/filter');
        Route::get('/orders/{id}');

        // products routes.
        Route::get('/products', [ProductController::class, 'getAllData']);
        Route::get('/products/{id}', [ProductController::class, 'getOneData']);
        Route::get('/products/search', [ProductController::class, 'getAllDataByQuery']);
        Route::get('/products/filter', [ProductController::class,'getAllDataByFilter' ]);

        // brands routes.
        Route::get('/brands', []);
        Route::get('/brands/search', []);
        Route::get('/brands/filter', []);
        Route::get('/brands/{id}', []);

        // returs routes.
    });
});
