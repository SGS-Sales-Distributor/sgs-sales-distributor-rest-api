<?php

use App\Http\Controllers\Api\AdminController;
use App\Http\Controllers\Api\JwtAuthentication\JwtAuthController;
use App\Http\Controllers\Api\MasterCallPlanController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\ProfilVisitController;
use App\Http\Controllers\Api\ProgramController;
use App\Http\Controllers\Api\ProgramTypeController;
use App\Http\Controllers\Api\PurchaseOrderController;
use App\Http\Controllers\Api\SalesmanController;
use App\Http\Controllers\Api\StoreInfoDistriController;
use App\Http\Middleware\JwtAuthMiddleware;
use Illuminate\Support\Facades\Route;

Route::group([
    'prefix' => 'sgs',
], function () {
    // visit's routes
    Route::get('/profil_visit', [ProfilVisitController::class, 'getAll']);
    Route::get('/profil_visit/{id}', [ProfilVisitController::class, 'getOne']);
    Route::put('/profil_visit/{id}', [ProfilVisitController::class, 'updateOne']);
    Route::delete('/profil_visit/{id}', [ProfilVisitController::class, 'removeOne']);

    // order's routes.
    Route::get('/orders', [PurchaseOrderController::class, 'getAll']);
    Route::get('/orders/{id}', [PurchaseOrderController::class, 'getOne']);
    Route::post('/orders', [PurchaseOrderController::class, 'storeOne']);
    Route::put('/orders/{id}', [PurchaseOrderController::class, 'updateOne']);
    Route::delete('/orders/{id}', [PurchaseOrderController::class, 'removeOne']);

    // call plan's routes.
    Route::get('/call-plans', [MasterCallPlanController::class, 'getAll']);
    Route::get('/call-plans/filter', [MasterCallPlanController::class, 'getAllByDateFilter']);
    Route::post('/call-plans', [MasterCallPlanController::class, 'storeOne']);
    Route::put('/call-plans/{id}', [MasterCallPlanController::class, 'updateOne']);
    Route::delete('/call-plans/{id}', [MasterCallPlanController::class, 'removeOne']);
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
        Route::post('/register', [JwtAuthController::class, 'register']);
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

        // salesman's routes.
        Route::get('/salesmen', [SalesmanController::class, 'getAll']);
        Route::get('/salesmen/{number}', [SalesmanController::class, 'getOne']);
        Route::post('/salesmen/{number}/visits', [SalesmanController::class, 'checkInVisit']);
        Route::post('/salesmen/{number}/visits/{visitId}', [SalesmanController::class, 'checkOutVisit']);
        Route::put('/salesmen/{number}', [SalesmanController::class, 'updateOne']);
        Route::patch('/salesmen/{number}/profiles', [SalesmanController::class, 'updateProfile']);
        Route::patch('/salesmen/{number}/change-password', [SalesmanController::class, 'changePassword']);
        Route::delete('/salesmen/{number}', [SalesmanController::class, 'removeOne']);
        Route::get('/salesmen/{number}/visits', [SalesmanController::class, 'getVisits']);
        Route::get('/salesmen/{number}/visits/{visitId}', [SalesmanController::class, 'getOneVisit']);
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
        Route::delete('/products/{number}', [ProductController::class, 'removeOne']);

        // store's routes.
        Route::get('/stores', [StoreInfoDistriController::class, 'getAll']);        
        Route::get('/stores/filter', [StoreInfoDistriController::class, 'getAllDataByOrderDateFilter']);
        Route::get('/stores/{id}', [StoreInfoDistriController::class, 'getOne']);
        Route::post('/stores', [StoreInfoDistriController::class, 'storeOne']);
        Route::put('/stores/{id}', [StoreInfoDistriController::class, 'updateOne']);
        Route::delete('/stores/{id}', [StoreInfoDistriController::class, 'removeOne']);
        
        // store type & cabang routes.
        Route::get('/store-types', [StoreInfoDistriController::class, 'getStoreTypes']);
        Route::get('/store-cabangs', [StoreInfoDistriController::class, 'getStoreCabangs']);

        // store visit's routes.
        Route::get('/stores/{id}/visits', [StoreInfoDistriController::class, 'getAllVisits']);
        Route::get('/stores/{id}/visits/{visitId}', [StoreInfoDistriController::class, 'getOneVisit']);
        
        // store purchase order's routes.
        Route::get('/stores/{id}/orders', [StoreInfoDistriController::class, 'getAllOrders']);
        Route::get('/stores/{id}/orders/{orderId}', [StoreInfoDistriController::class, 'getOneOrder']);

        // store owner's routes.
        Route::get('/stores/{id}/owners', [StoreInfoDistriController::class, 'getAllOwners']);
        Route::get('/stores/{id}/owners/{ownerId}', [StoreInfoDistriController::class, 'getOneOwner']);
        Route::post('/stores/{id}/owners', [StoreInfoDistriController::class, 'storeOwner']);
        Route::put('/stores/{id}/owners/{ownerId}', [StoreInfoDistriController::class, 'updateOwner']);
        Route::delete('/stores/{id}/owners/{ownerId}', [StoreInfoDistriController::class, 'removeOneOwner']);
        
        // store send otp's routes.
        Route::post('/stores/send-otp', [StoreInfoDistriController::class, 'sendOtp']);
        Route::post('/stores/resend-otp', [StoreInfoDistriController::class, 'resendOtp']);
        Route::post('/stores/confirm-otp', [StoreInfoDistriController::class, 'confirmOtp']);
    });
});
