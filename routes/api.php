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
use App\Http\Controllers\Api\StoreTypeController;
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

    // type program's routes.
    Route::get('/master_type_program_x', [ProgramTypeController::class, 'getAll']);
    Route::get('/master_type_program/{id}', [ProgramTypeController::class, 'getOne']);
    Route::post('/master_type_program', [ProgramTypeController::class, 'storeOne']);
    Route::put('/master_type_program/{id}', [ProgramTypeController::class, 'updateOne']);
    Route::delete('/master_type_program/{id}', [ProgramTypeController::class, 'removeOne']);

    Route::get('/type-programs/{id}', [ProgramTypeController::class, 'getOne']);
    Route::post('/type-programs', [ProgramTypeController::class, 'storeOne']);
    Route::put('/type-programs', [ProgramTypeController::class, 'updateOne']);
    Route::delete('/type-programs', [ProgramTypeController::class, 'removeOne']);

    Route::get('/getTipeToko', [StoreTypeController::class, 'getTipeToko']);
    Route::get('/store_type', [StoreTypeController::class, 'paging']);
    Route::post('/store_type', [StoreTypeController::class, 'store']);
    Route::get('/store_type/{store_type_id}', [StoreTypeController::class, 'show']);
    Route::put('/store_type/{store_type_id}', [StoreTypeController::class, 'update']);
    Route::delete('/store_type/{store_type_id}', [StoreTypeController::class, 'destroy']);
    
    // // CRUD untuk Product Info Do
    Route::get('/getMasterProduk', [ProductController::class, 'getMasterProduk']);
    Route::get('/product_info_do', [ProductController::class, 'paging']);
    Route::post('/product_info_do', [ProductController::class, 'store']);
    Route::get('/product_info_do/{prod_number}', [ProductController::class, 'show']);
    Route::put('/product_info_do/{prod_number}', [ProductController::class, 'update']);
    Route::delete('/product_info_do/{prod_number}', [ProductController::class, 'destroy']);
    
    // CRUD untuk User Info 
    // Router::get('/getUserInfo', 'UserInfoController@getUserInfo');
    // Router::get('/getUserInfoX', 'UserInfoController@getUserInfoX');
    // Router::get('/user_info', 'UserInfoController@paging');
    // Router::post('/user_info', 'UserInfoController@store');
    // Router::get('/user_info/{user_id}', 'UserInfoController@show');
    // Router::put('/user_info/{user_id}', 'UserInfoController@update');
    // Router::delete('/user_info/{user_id}', 'UserInfoController@destroy');
    // Router::get('/getCboUserType', 'UserInfoController@getCboUserType');
    // Router::get('/getCboStoreCabang', 'UserInfoController@getCboStoreCabang');
    Route::get('/getCboOutlet', 'UserInfoController@getCboOutlet');
    // Router::get('/getCboPenempatan', 'UserInfoController@getCboPenempatan');
    // Router::get('/getKodeLokasi/{id}', 'UserInfoController@getKodeLokasi');
    // Router::post('/exportData', 'UserInfoController@xportData');

    // CRUD untuk Master Type Program 
    // Router::get('/getTipeProgram', 'MasterTypeProgramController@getTipeProgram');
    // Router::get('/master_type_program_x', 'MasterTypeProgramController@paging');

    // CRUD untuk Kode Lokasi
    // Router::get('/kode_lokasi', 'KodeLokasiController@paging');

    // CRUD untuk Store Cabang
    // Router::get('/store_cabang', 'StoreCabangController@paging');
    // Router::post('/store_cabang', 'StoreCabangController@store');
    // Router::get('/store_cabang/{id}', 'StoreCabangController@show');
    // Router::put('/store_cabang/{id}', 'StoreCabangController@update');
    // Router::delete('/store_cabang/{id}', 'StoreCabangController@destroy');

    // CRUD untuk Store Info Distri
    // Router::get('/getcboIDCabang', 'StoreInfoDistriController@getcboIDCabang');
    // Router::get('/getcboIDStore', 'StoreInfoDistriController@getcboIDStore');
    // Router::get('/getStoreInfoDistri', 'StoreInfoDistriController@getStoreInfoDistri');
    // Router::post('/exportDataToko', 'UserInfoController@xportDataToko');
    // Router::get('/store_info_distri', 'StoreInfoDistriController@paging');
    // Router::post('/store_info_distri', 'StoreInfoDistriController@store');
    // Router::get('/store_info_distri/{store_id}', 'StoreInfoDistriController@show');
    // Router::put('/store_info_distri/{store_id}', 'StoreInfoDistriController@update');
    // Router::delete('/store_info_distri/{store_id}', 'StoreInfoDistriController@destroy');

    // CRUD untuk Store Cabang
    // Router::get('visits', 'ProfilVisitController@paging');
    // Router::post('visits', 'ProfilVisitController@store');
    // Router::get('visits/{id}', 'ProfilVisitController@show');
    // Router::put('visits/{id}', 'ProfilVisitController@update');
    // Router::delete('visits/{id}', 'ProfilVisitController@destroy');

    // CRUD untuk User Type
    // Router::get('/user_type', 'UserTypeController@paging');
    // Router::post('/user_type', 'UserTypeController@store');
    // Router::get('/user_type/{user_type_id}', 'UserTypeController@show');
    // Router::put('/user_type/{user_type_id}', 'UserTypeController@update');
    // Router::delete('/user_type/{user_type_id}', 'UserTypeController@destroy');

    // CRUD untuk Store Type

    // // CRUD untuk Data Retur
    // Router::get('/data_retur', 'DataReturController@paging');
    // Router::post('/data_retur', 'DataReturController@store');
    // Router::get('/data_retur/{id}', 'DataReturController@show');
    // Router::put('/data_retur/{id}', 'DataReturController@update');
    // Router::delete('/data_retur/{id}', 'DataReturController@destroy');

    // // CRUD untuk Data Retur Detail
    // Router::get('/data_returdetail', 'DataReturDetailController@paging');
    // Router::post('/data_returdetail', 'DataReturDetailController@store');
    // Router::get('/data_returdetail/{id}', 'DataReturDetailController@show');
    // Router::put('/data_returdetail/{id}', 'DataReturDetailController@update');
    // Router::delete('/data_returdetail/{id}', 'DataReturDetailController@destroy');

    // // CRUD untuk Master User
    // Router::get('/master_user', 'MasterUserController@paging');
    // Router::post('/master_user', 'MasterUserController@store');
    // Router::get('/master_user/{id}', 'MasterUserController@show');
    // Router::put('/master_user/{id}', 'MasterUserController@update');
    // Router::delete('/master_user/{id}', 'MasterUserController@destroy');

    // // CRUD untuk Master User Detail
    // Router::get('/master_user_detail', 'MasterUserDetailController@paging');
    // Router::post('/master_user_detail', 'MasterUserDetailController@store');
    // Router::get('/master_user_detail/{id}', 'MasterUserDetailController@show');
    // Router::put('/master_user_detail/{id}', 'MasterUserDetailController@update');
    // Router::delete('/master_user_detail/{id}', 'MasterUserDetailController@destroy');

    // // CRUD untuk Product Info Lmt
    // Router::get('/product_info_lmt', 'ProductInfoLmtController@paging');
    // Router::post('/product_info_lmt', 'ProductInfoLmtController@store');
    // Router::get('/product_info_lmt/{prod_number}', 'ProductInfoLmtController@show');
    // Router::put('/product_info_lmt/{prod_number}', 'ProductInfoLmtController@update');
    // Router::delete('/product_info_lmt/{prod_number}', 'ProductInfoLmtController@destroy');

    // // CRUD untuk Order Customer Sales
    // Router::get('/getIDstore', 'OrderCustomerSalesController@getIDstore');
    // Router::get('/getPurchaseOrder', 'OrderCustomerSalesController@getPurchaseOrder');
    // Router::get('/order_customer_sales', 'OrderCustomerSalesController@paging');
    // Router::post('/order_customer_sales', 'OrderCustomerSalesController@store');
    // Router::get('/order_customer_sales/{id}', 'OrderCustomerSalesController@show');
    // Router::put('/order_customer_sales/{id}', 'OrderCustomerSalesController@update');
    // Router::delete('/order_customer_sales/{id}', 'OrderCustomerSalesController@destroy');

    // // CRUD untuk Order Customer Sales Detail
    // Router::get('/order_customer_sales_detail', 'OrderCustomerSalesDetailController@paging');
    // Router::post('/order_customer_sales_detail', 'OrderCustomerSalesDetailController@store');
    // Router::get('/order_customer_sales_detail/{id}', 'OrderCustomerSalesDetailController@show');
    // Router::put('/order_customer_sales_detail/{id}', 'OrderCustomerSalesDetailController@update');
    // Router::delete('/order_customer_sales_detail/{id}', 'OrderCustomerSalesDetailController@destroy');
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
