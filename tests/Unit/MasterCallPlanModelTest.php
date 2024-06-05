<?php

namespace Tests\Unit;

use App\Models\MasterCallPlan;
use App\Models\MasterCallPlanDetail;
use App\Models\MasterProvince;
use App\Models\StoreCabang;
use App\Models\StoreInfoDistri;
use App\Models\StoreType;
use App\Models\User;
use App\Models\UserStatus;
use App\Models\UserType;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class MasterCallPlanModelTest extends TestCase
{
    use DatabaseTransactions;

    // public function test_create_master_call_plan_and_detail_data(): void
    // {
    //     $userType = new UserType([
    //         'user_type_name' => 'test',
    //         'created_by' => 'john',
    //         'updated_by' => 'john', 
    //     ]);

    //     $userStatus = new UserStatus([
    //         'status' => 'test',
    //         'created_by' => 'john',
    //         'updated_by' => 'john', 
    //     ]);

    //     $userInfo = new User([
    //         'user_number' => '123',
    //         'user_nik' => '123',
    //         'user_fullname' => 'test',
    //         'user_phone' => '123',
    //         'user_email' => 'test@example.com',
    //         'user_name' => 'test123',
    //         'user_password' => 'test123',
    //         'user_type_id' => $userType->user_type_id,
    //         'user_status' => $userStatus->id,
    //         'cabang_id' => 1,
    //         'store_id' => 1,
    //         'status_ba' => 'test',
    //         'created_by' => 'john',
    //         'updated_by' => 'john', 
    //     ]);

    //     $masterCallPlan = new MasterCallPlan([
    //         'month_plan' => 1,
    //         'year_plan' => 1,
    //         'user_id' => $userInfo->user_id,
    //         'created_by' => 'john',
    //         'updated_by' => 'john', 
    //     ]);

    //     $storeType = new StoreType([
    //         'store_type_name' => 'test',
    //         'created_by' => 'john',
    //         'updated_by' => 'john', 
    //     ]);

    //     $masterProvince = new MasterProvince([
    //         'province' => 'test',
    //         'created_by' => 'john',
    //         'updated_by' => 'john', 
    //     ]);

    //     $storeCabang = new StoreCabang([
    //         'province_id' => $masterProvince->id_province,
    //         'kode_cabang' => '123',
    //         'nama_cabang' => 'TEST',
    //         'created_by' => 'john',
    //         'updated_by' => 'john', 
    //     ]);

    //     $storeInfoDistri = new StoreInfoDistri([
    //         'store_name' => 'test',
    //         'store_alias' => 'TEST',
    //         'store_address' => 'test',
    //         'store_phone' => '123',
    //         'store_fax' => '(123) 123',
    //         'store_type_id' => $storeType->store_type_id,
    //         'subcabang_id' => $storeCabang->id,
    //         'store_code' => '123',
    //         'active' => 1,
    //         'subcabang_idnew' => $storeCabang->id,
    //         'created_by' => 'john',
    //         'updated_by' => 'john', 
    //     ]);

    //     $masterCallPlanDetail = new MasterCallPlanDetail([
    //         'call_plan_id' => $masterCallPlan->id,
    //         'store_id' => $storeInfoDistri->store_id,
    //         'date' => Carbon::now(env('APP_TIMEZONE'))->format('Y-m-d'),
    //         'created_by' => 'john',
    //         'updated_by' => 'john', 
    //     ]);

    //     $masterCallPlanDetail->save();

    //     $this->assertNotNull($masterCallPlanDetail);

    //     $this->assertTrue($masterCallPlanDetail->exists);

    //     $this->assertNotNull($masterCallPlanDetail->created_at);
    // }
}
