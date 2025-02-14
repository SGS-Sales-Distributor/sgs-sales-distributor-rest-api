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
use App\Http\Controllers\Api\BrandController;
use App\Http\Controllers\Api\KodeLokasiController;
use App\Http\Controllers\Api\ProfilNotvisit;
use App\Http\Controllers\Api\StoreCabangController;
use App\Http\Controllers\AttendeeController;
use App\Http\Middleware\JwtAuthMiddleware;
use App\Http\Controllers\Api\OrderCustomerSalesController;
use Illuminate\Support\Facades\Route;
use App\Models\sts_jabatan;

use App\Http\Controllers\Api\OrderCustomerSalesDetailController;


// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');

// rest api version 1
Route::group([
    'prefix' => 'sgs',
], function () {
    // visit's routes
    Route::get('/profil_visit', [ProfilVisitController::class, 'getAll']);
    Route::get('/profil_visit/{id}', [ProfilVisitController::class, 'getOne']);
    Route::put('/profil_visit/{id}', [ProfilVisitController::class, 'updateOne']);
    Route::delete('/profil_visit/{id}', [ProfilVisitController::class, 'removeOne']);
    Route::get('/profil_visit/user/{userId}', [ProfilVisitController::class, 'getVisitUser']);
    Route::get('/profil_visit/user/{userId}/filter', [ProfilVisitController::class, 'getVisitUserByTanggal']);

    // Not visit's routes
    Route::get('/profil_notvisitOne/{id}', [ProfilNotvisit::class, 'getOneData']);

    //kodelokasi route
    // Route::get('/area', [KodeLokasiController::class, 'getAll']);


    // call plan's routes.
    Route::get('/call-plans', [MasterCallPlanController::class, 'getAll']);
    Route::get('/call-plans/filter', [MasterCallPlanController::class, 'getAllByDateFilter']);
    Route::post('/call-plans', [MasterCallPlanController::class, 'storeOne']);
    Route::put('/call-plans/{id}', [MasterCallPlanController::class, 'updateOne']);
    Route::delete('/call-plans/{id}', [MasterCallPlanController::class, 'removeOne']);
    Route::get('/call-plans/notVisited/{UserId}', [MasterCallPlanController::class, 'notVisitedUser']);

    // type program's routes.
    Route::get('/master_type_program_x', [ProgramTypeController::class, 'getAll']);
    Route::get('/master_type_program/{id}', [ProgramTypeController::class, 'getOne']);
    Route::post('/master_type_program', [ProgramTypeController::class, 'storeOne']);
    Route::put('/master_type_program/{id}', [ProgramTypeController::class, 'updateOne']);
    Route::delete('/master_type_program/{id}', [ProgramTypeController::class, 'removeOne']);

    Route::get('/getTipeToko', [StoreTypeController::class, 'getTipeToko']);
    Route::get('/store_type', [StoreTypeController::class, 'paging']);
    Route::post('/store_type', [StoreTypeController::class, 'store']);
    Route::get('/store_type/{store_type_name}', [StoreTypeController::class, 'show']);
    Route::put('/store_type/{store_type_id}', [StoreTypeController::class, 'update']);
    Route::delete('/store_type/{store_type_id}', [StoreTypeController::class, 'destroy']);

    // // CRUD untuk Product Info Do
    Route::get('/getMasterProduk', [ProductController::class, 'getAllBasic']);
    Route::get('/product_info_do', [ProductController::class, 'getAllBasicWithPaging']);
    Route::get('/product_info_do/{prod_number}', [ProductController::class, 'getOneBasic']);
    Route::post('/product_info_do', [ProductController::class, 'storeBasicData']);
    Route::put('/product_info_do/{prod_number}', [ProductController::class, 'updateBasicData']);
    Route::delete('/product_info_do/{prod_number}', [ProductController::class, 'removeBasicData']);

    Route::get('/order_customer_sales', [PurchaseOrderController::class, 'getAll']);
    Route::post('/order_customer_sales', [PurchaseOrderController::class, 'storeOne']);
    Route::get('/order_customer_sales/{id}', [PurchaseOrderController::class, 'getOne']);
    Route::put('/order_customer_sales/{id}', [PurchaseOrderController::class, 'updateOne']);
    Route::delete('/order_customer_sales/{id}', [PurchaseOrderController::class, 'removeOne']);

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
    Route::get('/area', action: [KodeLokasiController::class, 'getAll']);

    // for create new salesman account.
    Route::post('/salesmen', [SalesmanController::class, 'storeOneData']);

    // for create new admin account.
    Route::post('/admins', [AdminController::class, 'storeOneData']);

    Route::group([
        'middleware' => JwtAuthMiddleware::class,
    ], function () {
        // salesman's routes
        Route::get('/salesmen', [SalesmanController::class, 'getAll']);
        Route::get('/salesmen/{number}', [SalesmanController::class, 'getOne']);
        Route::post('/salesmen/{number}/visits', [SalesmanController::class, 'checkInVisit']);
        Route::post('/salesmen/{number}/visits/{visitId}', [SalesmanController::class, 'checkOutVisit']);
        Route::put('/salesmen/{number}', [SalesmanController::class, 'updateOne']);
        Route::patch('/salesmen/{number}/profiles', [SalesmanController::class, 'updateProfile']);
        Route::delete('/salesmen/{number}', [SalesmanController::class, 'removeOne']);
        Route::get('/salesmen/{number}/visits', [SalesmanController::class, 'getVisits']);
        Route::get('/salesmen/{number}/visits/{visitId}', [SalesmanController::class, 'getOneVisit']);
        Route::get('/salesmen/{number}/call-plans', [SalesmanController::class, 'getCallPlans']);
        Route::get('/salesmen/{number}/call-plans/{callPlanId}', [SalesmanController::class, 'getOneCallPlan']);
        Route::get('/salesmenById/{user_id}', [SalesmanController::class, 'getUserOne']);


        // brand's routes.
        Route::get('/brands', [BrandController::class, 'getAll']);
        Route::get('/brands/{id}', [BrandController::class, 'getOne']);

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

        Route::get('/store-cabangs', [StoreInfoDistriController::class, 'getStoreCabangs']);
        Route::get('/store-types', [StoreInfoDistriController::class, 'getStoreTypes']);

        // store's routes.
        Route::get('/stores', [StoreInfoDistriController::class, 'getAllWithoutCallPlans']);
        Route::get('/stores/call-plans', [StoreInfoDistriController::class, 'getAll']);
        Route::get('/stores/{id}/', [StoreInfoDistriController::class, 'getOne']);
        Route::post('/stores', [StoreInfoDistriController::class, 'storeOne']);
        Route::put('/stores/{id}', [StoreInfoDistriController::class, 'updateOne']);
        Route::delete('/stores/{id}', [StoreInfoDistriController::class, 'removeOne']);
        Route::get('/storesInfo/{id}', [StoreInfoDistriController::class, 'showDataStoreInfoDist']);
        Route::get('/storesByUser/{userId}', [StoreInfoDistriController::class, 'getStoresByUser']);

        Route::get('/stores/{id}/owners', [StoreInfoDistriController::class, 'getAllOwners']);
        Route::get('/stores/{id}/owners/{ownerId}', [StoreInfoDistriController::class, 'getOneOwner']);
        Route::post('/stores/{id}/owners', [StoreInfoDistriController::class, 'storeOwner']);
        Route::put('/stores/{id}/owners/{ownerId}', [StoreInfoDistriController::class, 'updateOwner']);
        Route::delete('/stores/{id}/owners{ownerId}', [StoreInfoDistriController::class, 'removeOwner']);

        Route::get('/stores/{id}/visits', [StoreInfoDistriController::class, 'getAllVisits']);
        Route::get('/stores/{id}/visits/{visitId}', [StoreInfoDistriController::class, 'getOneVisit']);
        Route::get('/stores/{id}/orders', [StoreInfoDistriController::class, 'getAllOrders']);
        Route::get('/stores/{id}/orders/{orderId}', [StoreInfoDistriController::class, 'getOneOrder']);

        Route::post('/stores/send-otp', [StoreInfoDistriController::class, 'sendOtp']);
        Route::post('/stores/resend-otp', [StoreInfoDistriController::class, 'resendOtp']);
        Route::post('/stores/confirm-otp', [StoreInfoDistriController::class, 'confirmOtp']);
        Route::get('/stores/countPObyStore', [StoreInfoDistriController::class, 'countPObyStore']);
        Route::get('/stores/storeNameGet', [StoreInfoDistriController::class, 'storeNameGet']);

        
        Route::get('/distriByCbg/{idCab}', [StoreInfoDistriController::class, 'getStoreByCbg']);
        
        //plant's route
        Route::post('/plantsNotvisit', [ProfilNotvisit::class, 'saveOneData']);
        // Route::get('/profil_notvisitOne/{id}', [ProfilNotvisit::class, 'getOneData']);
        
        //Store Cabang
        Route::get('/cabangs', [StoreCabangController::class, 'paging']); //INI UNTUK PAGING 
        Route::get('/cabangsAll', [StoreCabangController::class, 'getAll']);
        Route::get('/cabangsByUser/{userId}', [StoreCabangController::class, 'getCabangByUser']);
        
        Route::post('/call-plans', [MasterCallPlanController::class, 'storeOne']);
        Route::get('/getCovPlans', [MasterCallPlanController::class, 'getCoverage_plan']);
        
        Route::get('/detailOrder/{id}', [OrderCustomerSalesController::class, 'showDetailOrder']);
        // Route::post('/stores/kirimOTP', [StoreInfoDistriController::class, 'generateOTP']);
        Route::get('/detail-order/{id}', [StoreInfoDistriController::class, 'getDetail']);
        Route::post('/stores/save-draft', [StoreInfoDistriController::class, 'saveDraft']);
        Route::post('/stores/change-draft-to-deliv', [StoreInfoDistriController::class, 'changeDraftToDeliv']);
        Route::put('/updateDetail/{id}', [StoreInfoDistriController::class, 'updateDetail']);
        Route::delete('/stores/delete-draft/{id}', [StoreInfoDistriController::class, 'deleteDraft']);

        Route::get('/getAbsen/{user_id}/',[AttendeeController::class,'getDataAbsen']);
        Route::post('/addIn/{number}', [AttendeeController::class,'addIn']);
        Route::post('/addOut/{number}/{attendId}', [AttendeeController::class,'addOut']);
        Route::get('/jabatanAll', [sts_jabatan::class,'get_data']);
        Route::get('/jabatanbyOne/{id}', [sts_jabatan::class,'get_data_by_id']);
    });
});
