<?php

namespace App\Repositories;

use App\Models\OrderCustomerSales;
use App\Models\OrderCustomerSalesDetail;
use App\Models\ProfilVisit;
use App\Models\PurchaseOrderOTP;
use App\Models\StoreCabang;
use App\Models\StoreInfoDistri;
use App\Models\StoreInfoDistriPerson;
use App\Models\StoreType;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class StoreRepository extends Repository implements StoreInterface
{
    public static function str_random($length = 16)
    {
        $pool = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';

        return substr(str_shuffle(str_repeat($pool, 5)), 0, $length);
    }

    protected function generateOTP($id_table, $nomor_po)
    {
        $randomOtp = rand(1000, 9999);

        try {
            DB::beginTransaction();

            PurchaseOrderOTP::create([
                'id_po' => $id_table,
                'nomor_po' => $nomor_po,
                'random_otp' => $randomOtp,
            ]);

            DB::commit();

            return $randomOtp;
        } catch (\Symfony\Component\HttpKernel\Exception\HttpException $e) {
            DB::rollBack();

            return $this->errorResponse(
                statusCode: $e->getStatusCode(),
                success: false,
                msg: $e->getMessage(),
            );
        } catch (\Error $e) {
            DB::rollBack();

            return $this->errorResponse(
                statusCode: 500,
                success: false,
                msg: $e->getMessage(),
            );
        } catch (\Exception $e) {
            DB::rollBack();

            return $this->errorResponse(
                statusCode: 500,
                success: false,
                msg: $e->getMessage(),
            );
        }
    }

    public function getAllData(Request $request): JsonResponse
    {
        $jwt = $request->decoded_token;

        $decryptUser = $this->jwtAuthToken->decryptUserData($jwt['user']);

        $searchByQuery = $request->query('q');

        // DB::enableQueryLog();

        $currDate = now(env('APP_TIMEZONE'))->format('Y-m-d');

        $subQuery = DB::table('profil_visit')
            ->select('profil_visit.*')
            ->where('profil_visit.tanggal_visit', $currDate);

        $stores = DB::table('master_call_plan')
            ->select([
                'store_info_distri.store_id',
                'store_info_distri.store_name as nama_toko',
                'store_info_distri.store_alias as alias_toko',
                'store_info_distri.store_address as alamat_toko',
                'store_info_distri.store_phone as nomor_telepon_toko',
                'store_info_distri.store_fax as nomor_fax_toko',
                'store_info_distri.store_type_id',
                'store_info_distri.subcabang_id',
                'store_info_distri.store_code as kode_toko',
                'store_info_distri.active as status_toko',
                'pv.id as visit_id',
                'pv.user as nama_salesman',
                'pv.tanggal_visit as tanggal_visit',
                'pv.time_in as waktu_masuk',
                'pv.time_out as waktu_keluar',
                'pv.ket as keterangan',
                'pv.approval as approval',
                'master_call_plan.*',
                'master_call_plan_detail.store_id',
                'master_call_plan_detail.date',
                'user_info.fullname as nama_salesman',
                'user_info.nik as nik_salesman',
                'user_info.email as email_salesman',
            ])
            ->join('master_call_plan_detail', 'master_call_plan.id', '=', 'master_call_plan_detail.call_plan_id')
            ->join('user_info', 'user_info.user_id', '=', 'master_call_plan.user_id')
            ->join('store_info_distri', 'store_info_distri.store_id', '=', 'master_call_plan_detail.store_id')
            ->leftJoinSub($subQuery, 'pv', function ($join) {
                $join->on('pv.tanggal_visit', '=', 'master_call_plan_detail.date')
                    ->on('pv.store_id', '=', 'master_call_plan_detail.store_id');
            })
            ->where('user_info.number', '=', $decryptUser["number"])
            ->where('master_call_plan_detail.date', '=', $currDate)
            ->when($searchByQuery, function ($query) use ($searchByQuery) {
                $query->where('store_info_distri.store_name', 'LIKE', '%' . $searchByQuery . '%');
            })
            ->orderBy('store_info_distri.store_name', 'asc')
            ->get();

        // dd(DB::getQueryLog());

        return $this->successResponse(
            statusCode: 200,
            success: true,
            msg: "Successfully fetch store.",
            resource: $stores,
        );
    }

    public function getAllDataWithoutCallPlans(Request $request): JsonResponse
    {
        $searchByQuery = $request->query('q');

        $storeCallPlansCache = Cache::remember(
            "storeCallPlansCache",
            $this::DEFAULT_CACHE_TTL,
            function () use ($searchByQuery) {
                return StoreInfoDistri::with('owners')
                    ->select(
                        'store_id',
                        'store_name as nama_toko',
                        'store_alias as alias_toko',
                        'store_address as alamat_toko',
                        'store_phone as nomor_telepon_toko',
                        'store_fax as nomor_fax_toko',
                        'store_type_id',
                        'subcabang_id',
                        'store_code as kode_toko',
                        'active as status_toko',
                    )->when($searchByQuery, function (EloquentBuilder $query) use ($searchByQuery) {
                        $query->where('store_name', 'LIKE', '%' . $searchByQuery . '%');
                    })->whereHas('owners')
                    ->orderBy('store_name', 'asc')
                    ->paginate($this::DEFAULT_PAGINATE);
            }
        );

        return $this->successResponse(
            statusCode: 200,
            success: true,
            msg: "Successfully fetch store based on call plans",
            resource: $storeCallPlansCache,
        );
    }

    public function getOneData(int $id): JsonResponse
    {
        $currDate = now(env('APP_TIMEZONE'))->format('Y-m-d');

        $store = DB::table('master_call_plan')
            ->select([
                'master_call_plan_detail.store_id',
                'store_info_distri.store_name as nama_toko',
                'store_info_distri.store_alias as alias_toko',
                'store_info_distri.store_address as alamat_toko',
                'store_info_distri.store_phone as nomor_telepon_toko',
                'store_info_distri.store_fax as nomor_fax_toko',
                'store_info_distri.store_type_id',
                'store_info_distri.subcabang_id',
                'store_info_distri.store_code as kode_toko',
                'store_info_distri.active as status_toko',
                'profil_visit.id as visit_id',
                'profil_visit.user as nama_salesman',
                'profil_visit.tanggal_visit as tanggal_visit',
                'profil_visit.time_in as waktu_masuk',
                'profil_visit.time_out as waktu_keluar',
                'profil_visit.ket as keterangan',
                'profil_visit.approval as approval',
                'master_call_plan.*',
                'master_call_plan_detail.date',
                'user_info.fullname as nama_salesman',
                'user_info.nik as nik_salesman',
                'user_info.email as email_salesman',
            ])
            ->join('master_call_plan_detail', 'master_call_plan.id', '=', 'master_call_plan_detail.call_plan_id')
            ->join('user_info', 'user_info.user_id', '=', 'master_call_plan.user_id')
            ->join('store_info_distri', 'store_info_distri.store_id', '=', 'master_call_plan_detail.store_id')
            ->leftJoin('profil_visit', function ($join) {
                $join->on('profil_visit.tanggal_visit', '=', 'master_call_plan_detail.date')
                    ->on('profil_visit.store_id', '=', 'master_call_plan_detail.store_id'); // Ensure correct store and visit date match
            })
            ->where('user_info.user_id', '=', 1) // Assuming the user_id is an integer
            ->where('master_call_plan_detail.date', '=', $currDate)
            ->where('store_info_distri.store_id', '=', $id)
            ->first();

        return $this->successResponse(
            statusCode: 200,
            success: true,
            msg: "Successfully fetch store {$id}.",
            resource: $store
        );
    }

    public function getOneDatawithoutCallPlan(int $id): JsonResponse
    {
        $store = DB::table('store_info_distri')
            ->select(
                'store_info_distri.store_id',
                'store_info_distri.store_name as nama_toko',
                'store_info_distri.store_alias as alias_toko',
                'store_info_distri.store_address as alamat_toko',
                'store_info_distri.store_phone as nomor_telepon_toko',
                'store_info_distri.store_fax as nomor_fax_toko',
                'store_info_distri.store_type_id',
                'store_info_distri.subcabang_id',
                'store_info_distri.store_code as kode_toko',
                'store_info_distri.active as status_toko',
                'store_info_distri_person.owner as nama_pemilik',
                'store_info_distri_person.nik_owner as nik_pemilik',
                'store_info_distri_person.email_owner as email_pemilik',
            )->join('store_info_distri_person', 'store_info_distri_person.store_id', '=', 'store_info_distri.store_id')
            ->where('store_info_distri.store_id', '=', $id)
            ->first();

        return $this->successResponse(
            statusCode: 200,
            success: true,
            msg: "Successfully fetch store {$id}.",
            resource: $store
        );
    }

    public function storeOneData(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'store_name' => ['required', 'string', 'max:100'],
            'store_alias' => ['required', 'string', 'max:200'],
            'store_address' => ['required', 'string'],
            'store_phone' => ['required', 'string', 'max:20', 'unique:store_info_distri,store_phone'],
            'store_fax' => ['required', 'string', 'max:20', 'unique:store_info_distri,store_fax'],
            'store_type_id' => ['required', 'integer'],
            'subcabang_id' => ['required', 'integer'],
            'subcabang_idnew' => ['nullable', 'integer'],
            'store_code' => ['nullable', 'string', 'max:20'],
            'active' => ['nullable', 'integer'],
        ]);

        if ($validator->fails()) {
            return $this->clientErrorResponse(
                statusCode: 422,
                success: false,
                msg: $validator->errors()->first(),
            );
        }

        try {
            DB::beginTransaction();

            $store = StoreInfoDistri::create([
                'store_name' => $request->store_name,
                'store_alias' => $request->store_alias,
                'store_address' => $request->store_address,
                'store_phone' => $request->store_phone,
                'store_fax' => $request->store_fax,
                'store_type_id' => $request->store_type_id,
                'subcabang_id' => $request->subcabang_id,
                'subcabang_idnew' => $request->subcabang_id,
                'store_code' => $this->randomStoreCode->generateRandomCode(20),
                'active' => 1,
            ]);

            DB::commit();

            return $this->successResponse(
                statusCode: 201,
                success: true,
                msg: "Successfully create new outlet.",
                resource: $store
            );
        } catch (\Symfony\Component\HttpKernel\Exception\HttpException $e) {
            DB::rollBack();

            return $this->errorResponse(
                statusCode: $e->getStatusCode(),
                success: false,
                msg: $e->getMessage(),
            );
        } catch (\Error $e) {
            DB::rollBack();

            return $this->errorResponse(
                statusCode: 500,
                success: false,
                msg: $e->getMessage(),
            );
        } catch (\Exception $e) {
            DB::rollBack();

            return $this->errorResponse(
                statusCode: 500,
                success: false,
                msg: $e->getMessage(),
            );
        }
    }

    public function updateOneData(Request $request, int $id): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'store_name' => ['required', 'string', 'max:100'],
            'store_alias' => ['required', 'string', 'max:200'],
            'store_address' => ['required', 'string'],
            'store_phone' => ['required', 'string', 'max:20', 'unique:store_info_distri,store_phone'],
            'store_fax' => ['required', 'string', 'max:20', 'unique:store_info_distri,store_fax'],
            'store_type_id' => ['required', 'integer'],
            'subcabang_id' => ['required', 'integer'],
            'subcabang_idnew' => ['nullable', 'integer'],
            'store_code' => ['nullable', 'string', 'max:20'],
            'active' => ['nullable', 'integer'],
        ]);

        if ($validator->fails()) {
            return $this->clientErrorResponse(
                statusCode: 422,
                success: false,
                msg: $validator->errors()->first(),
            );
        }

        $store = StoreInfoDistri::where('store_id', $id)->firstOrFail();

        try {
            DB::beginTransaction();

            $store->update([
                'store_name' => $request->store_name,
                'store_alias' => $request->store_alias,
                'store_address' => $request->store_address,
                'store_phone' => $request->store_phone,
                'store_fax' => $request->store_fax,
                'store_type_id' => $request->store_type_id,
                'subcabang_id' => $request->subcabang_id,
                'store_code' => $store->store_code,
                'active' => $request->active,
            ]);

            DB::commit();

            return $this->successResponse(
                statusCode: 200,
                success: true,
                msg: "Successfully update recent outlet {$id}."
            );
        } catch (\Symfony\Component\HttpKernel\Exception\HttpException $e) {
            DB::rollBack();

            return $this->errorResponse(
                statusCode: $e->getStatusCode(),
                success: false,
                msg: $e->getMessage(),
            );
        } catch (\Error $e) {
            DB::rollBack();

            return $this->errorResponse(
                statusCode: 500,
                success: false,
                msg: $e->getMessage(),
            );
        } catch (\Exception $e) {
            DB::rollBack();

            return $this->errorResponse(
                statusCode: 500,
                success: false,
                msg: $e->getMessage(),
            );
        }
    }

    public function removeOneData(int $id): JsonResponse
    {
        $store = StoreInfoDistri::findOrFail($id);

        $store->delete();

        return $this->successResponse(
            statusCode: 200,
            success: true,
            msg: "Successfully remove recent outlet {$id}."
        );
    }

    public function sendOtp(Request $request): JsonResponse
    {
        $store = StoreInfoDistri::where('store_id', $request->idToko)
            ->firstOrFail();

        try {
            DB::beginTransaction();

            $objectOrder = $request->objOrder;

            $initialNum = 0;

            foreach ($objectOrder as $key => $value) {
                $initialNum += $objectOrder[$key]['qty'];
            };

            $nomor_po = date('YmdHis') . $request->idToko;

            $id_table = OrderCustomerSales::create([
                'no_order' => $nomor_po,
                'tgl_order' => date('Y-m-d'),
                'tipe' => 'SO',
                'company' => 1,
                'top' => 30,
                'cust_code' => str_replace('-', '', $store->store_code),
                'ship_code' => '000',
                'whs_code' => '016',
                'whs_code_to' => 0,
                'order_sts' => 'Draft',
                'totOrderQty' => $initialNum,
                'totReleaseQty' => 0,
                'keterangan' => $request->metodePembayaran,
                'llb_gabungan_reff' => null,
                'llb_gabungan_sts' => "Open",
                'uploaded_at' => date('Y-m-d H:i:s'),
                'uploaded_by' => "iduser",
                'store_id' => $request->idToko,
                'status_id' => 1,
            ])->id;

            $n = 0;

            foreach ($objectOrder as $key => $value) {
                $n++;

                OrderCustomerSalesDetail::create([
                    'orderId' => $id_table,
                    'lineNo' => $n,
                    'itemCodeCust' => $objectOrder[$key]['prodNumber'],
                    'itemCode' => $objectOrder[$key]['prodNumber'],
                    'qtyOrder' => $objectOrder[$key]['qty'],
                    'add_disc_2' => $objectOrder[$key]['statusBonus'],
                ]);
            }

            $kode_otp = $this->generateOTP($id_table, $nomor_po);

            DB::commit();

            return $this->successResponse(
                statusCode: 201,
                success: true,
                msg: "Pembuatan draft PO berhasil!",
                resource: [
                    'nomor_po' => $nomor_po,
                    'otp' => $kode_otp,
                ],
            );
        } catch (\Symfony\Component\HttpKernel\Exception\HttpException $e) {
            DB::rollBack();

            return $this->errorResponse(
                statusCode: $e->getStatusCode(),
                success: false,
                msg: $e->getMessage(),
            );
        } catch (\Error $e) {
            DB::rollBack();

            return $this->errorResponse(
                statusCode: 500,
                success: false,
                msg: $e->getMessage(),
            );
        } catch (\Exception $e) {
            DB::rollBack();

            return $this->errorResponse(
                statusCode: 500,
                success: false,
                msg: $e->getMessage(),
            );
        }
    }

    public function resendOTP(Request $request): JsonResponse
    {
        try {
            DB::beginTransaction();

            $purchaseOrderOTP = PurchaseOrderOTP::where('nomor_po', $request->nomorPO)
                ->firstOrFail();

            $x = rand(1000, 9999);

            $purchaseOrderOTP->update([
                'random_otp' => $x,
            ]);

            DB::commit();

            return $this->successResponse(
                statusCode: 201,
                success: true,
                msg: "Resend OTP berhasil!",
                resource: [
                    'nomor_po' => $request->nomorPO,
                    'otp' => $x,
                ],
            );
        } catch (\Symfony\Component\HttpKernel\Exception\HttpException $e) {
            DB::rollBack();

            return $this->errorResponse(
                statusCode: $e->getStatusCode(),
                success: false,
                msg: $e->getMessage(),
            );
        } catch (\Error $e) {
            DB::rollBack();

            return $this->errorResponse(
                statusCode: 500,
                success: false,
                msg: $e->getMessage(),
            );
        } catch (\Exception $e) {
            DB::rollBack();

            return $this->errorResponse(
                statusCode: 500,
                success: false,
                msg: $e->getMessage(),
            );
        }
    }

    public function confirmOtp(Request $request): JsonResponse
    {
        $purchaseOrderOTP = PurchaseOrderOTP::where('nomor_po', $request->nomorPO)
            ->where('random_otp', $request->inputOtp)
            ->firstOrFail();

        // ubah PO ke 2
        $orderCustomerSales = OrderCustomerSales::where('no_order', $request->nomorPO)
            ->firstOrFail();

        try {
            DB::beginTransaction();

            $orderCustomerSales->update([
                'status_id' => 2,
            ]);

            DB::commit();

            return $this->successResponse(
                statusCode: 201,
                success: true,
                msg: "Konfirmasi PO anda berhasil!",
            );
        } catch (\Symfony\Component\HttpKernel\Exception\HttpException $e) {
            DB::rollBack();

            return $this->errorResponse(
                statusCode: $e->getStatusCode(),
                success: false,
                msg: $e->getMessage(),
            );
        } catch (\Error $e) {
            DB::rollBack();

            return $this->errorResponse(
                statusCode: 500,
                success: false,
                msg: $e->getMessage(),
            );
        } catch (\Exception $e) {
            DB::rollBack();

            return $this->errorResponse(
                statusCode: 500,
                success: false,
                msg: $e->getMessage(),
            );
        }
    }

    public function getAllDataByOrderDateFilter(Request $request): JsonResponse
    {
        $searchByOrderDateQuery = $request->query('q');

        $filterByDateRange = $this->dateRangeFilter->parseDateRange($searchByOrderDateQuery);

        $filterByDate = $this->dateRangeFilter->parseDate($searchByOrderDateQuery);

        $storeInfoDistriByTypeFilterCache = Cache::remember(
            'storeInfoDistriByTypeFilter',
            $this::DEFAULT_CACHE_TTL,
            function () use (
                $filterByDateRange,
                $filterByDate,
            ) {
                if ($filterByDateRange) {
                    return StoreInfoDistri::with([
                        'type',
                        'cabang',
                        'visits',
                        'owners',
                        'orders',
                        'orderDetails',
                        'masterCallPlanDetails',
                    ])->when($filterByDateRange, function (EloquentBuilder $query) use ($filterByDateRange) {
                        $query->whereHas('orders', function (EloquentBuilder $subQuery) use ($filterByDateRange) {
                            $subQuery->whereBetween('tgl_order', $filterByDateRange);
                        });
                    })
                        ->orderBy('store_id', 'asc')
                        ->paginate($this::DEFAULT_PAGINATE);
                }

                if ($filterByDate) {
                    return StoreInfoDistri::with([
                        'type',
                        'cabang',
                        'visits',
                        'owners',
                        'orders',
                        'orderDetails',
                        'masterCallPlanDetails',
                    ])->when($filterByDate, function (EloquentBuilder $query) use ($filterByDate) {
                        $query->whereHas('orders', function (EloquentBuilder $subQuery) use ($filterByDate) {
                            $subQuery->whereDate('tgl_order', $filterByDate);
                        });
                    })
                        ->orderBy('store_id', 'asc')
                        ->paginate($this::DEFAULT_PAGINATE);
                }
            }
        );

        return $this->successResponse(
            statusCode: 200,
            success: true,
            msg: "Successfully fetch store with store type filter {$request->input('type')}.",
            resource: $storeInfoDistriByTypeFilterCache,
        );
    }

    public function getAllOwnersData(Request $request, int $id): JsonResponse
    {
        $searchByQuery = $request->query('q');

        $storeOwnersCache = Cache::remember(
            "stores:{$id}:owners",
            $this::DEFAULT_CACHE_TTL,
            function () use ($searchByQuery, $id) {
                return StoreInfoDistriPerson::with([
                    'store',
                ])->whereHas('store', function (EloquentBuilder $query) use ($id) {
                    $query->where('id', $id);
                })->when($searchByQuery, function (EloquentBuilder $query) use ($searchByQuery) {
                    $query->where('owner', 'LIKE', '%' . $searchByQuery . '%');
                })->orderBy('owner', 'asc')
                    ->paginate($this::DEFAULT_PAGINATE);
            }
        );

        return $this->successResponse(
            statusCode: 200,
            success: true,
            msg: "Successfully fetch store owners.",
            resource: $storeOwnersCache,
        );
    }

    public function getOneOwnerData(int $id, int $ownerId): JsonResponse
    {
        $storeOwnerCache = Cache::remember(
            "stores:{$id}:owners:{$ownerId}",
            $this::DEFAULT_CACHE_TTL,
            function () use ($id, $ownerId) {
                return StoreInfoDistriPerson::with([
                    'store'
                ])->whereHas('store', function (EloquentBuilder $query) use ($id) {
                    $query->where('id', $id);
                })->where('id', $ownerId)
                    ->firstOrFail();
            }
        );

        return $this->successResponse(
            statusCode: 200,
            success: true,
            msg: "Successfully fetch store owner {$ownerId}.",
            resource: $storeOwnerCache,
        );
    }

    public function storeOneOwnersData(Request $request, int $id): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'owner' => ['required', 'string', 'max:255'],
            'nik_owner' => ['required', 'string', 'max:20'],
            'email_owner' => ['required', 'string', 'max:100'],
        ]);

        if ($validator->fails()) {
            return $this->clientErrorResponse(
                statusCode: 422,
                success: false,
                msg: $validator->errors()->first(),
            );
        }

        try {
            DB::beginTransaction();

            $ktp_image = $request->file('ktp_owner');

            $photo_other_image = $request->file('photo_other');

            $ktp_name = date('YmdHis') . '_' . $this->str_random(10) . '.' . 'png';

            $photo_other_name = date('YmdHis') . '_' . $this->str_random(10) . '.' . 'png';

            $ktp_destination_path = base_path('public/images/ktp');

            $photo_other_destination_path = base_path('public/images/other');

            $ktp_image->move($ktp_destination_path, $ktp_name);

            $photo_other_image->move($photo_other_destination_path, $photo_other_name);

            $storeOwner = StoreInfoDistriPerson::create([
                'store_id' => $id,
                'owner' => $request->owner,
                'nik_owner' => $request->nik_owner,
                'email_owner' => $request->email_owner,
                'ktp_owner' => $ktp_name ? $ktp_name : "",
                'photo_other' => $photo_other_name ? $photo_other_name : "",
            ]);

            DB::commit();

            return $this->successResponse(
                statusCode: 201,
                success: true,
                msg: "Successfully create new owner data for outlet {$id}.",
                resource: $storeOwner
            );
        } catch (\Symfony\Component\HttpKernel\Exception\HttpException $e) {
            DB::rollBack();

            return $this->errorResponse(
                statusCode: $e->getStatusCode(),
                success: false,
                msg: $e->getMessage(),
            );
        } catch (\Error $e) {
            DB::rollBack();

            return $this->errorResponse(
                statusCode: 500,
                success: false,
                msg: $e->getMessage(),
            );
        } catch (\Exception $e) {
            DB::rollBack();

            return $this->errorResponse(
                statusCode: 500,
                success: false,
                msg: $e->getMessage(),
            );
        }
    }

    public function updateOneOwnerData(Request $request, int $id, int $ownerId): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'owner' => ['required', 'string', 'max:255'],
            'nik_owner' => ['required', 'string', 'max:20'],
            'email_owner' => ['required', 'string', 'max:100'],
            'ktp_owner' => ['nullable', 'string', 'max:255'],
            'photo_other' => ['nullable', 'string', 'max:255'],
        ]);

        if ($validator->fails()) {
            return $this->clientErrorResponse(
                statusCode: 422,
                success: false,
                msg: $validator->errors()->first(),
            );
        }

        $storeOwner = StoreInfoDistriPerson::whereHas('store', function (EloquentBuilder $query) use ($id) {
            $query->where('store_id', $id);
        })->where('id', $ownerId)
            ->firstOrFail();

        try {
            DB::beginTransaction();

            $storeOwner->update([
                'owner' => $request->owner,
                'nik_owner' => $request->nik_owner,
                'email_owner' => $request->email_owner,
                'ktp_owner' => $request->ktp_owner,
                'photo_other' => $request->photo_other,
            ]);

            DB::commit();

            return $this->successResponse(
                statusCode: 200,
                success: true,
                msg: "Successfully update recent owner {$ownerId} data for outlet {$id}."
            );
        } catch (\Symfony\Component\HttpKernel\Exception\HttpException $e) {
            DB::rollBack();

            return $this->errorResponse(
                statusCode: $e->getStatusCode(),
                success: false,
                msg: $e->getMessage(),
            );
        } catch (\Error $e) {
            DB::rollBack();

            return $this->errorResponse(
                statusCode: 500,
                success: false,
                msg: $e->getMessage(),
            );
        } catch (\Exception $e) {
            DB::rollBack();

            return $this->errorResponse(
                statusCode: 500,
                success: false,
                msg: $e->getMessage(),
            );
        }
    }

    public function removeOneOwnerData(int $id, int $ownerId): JsonResponse
    {
        $storeOwner = StoreInfoDistriPerson::whereHas('store', function (EloquentBuilder $query) use ($id) {
            $query->where('store_id', $id);
        })->where('id', $ownerId)
            ->firstOrFail();

        $storeOwner->delete();

        return $this->successResponse(
            statusCode: 200,
            success: true,
            msg: "Successfully remove recent owner {$ownerId} data for outlet {$id}."
        );
    }

    public function getAllVisitsData(Request $request, int $id): JsonResponse
    {
        $searchByQuery = $request->query('q');

        $storeVisitsCache = Cache::remember(
            "stores:{$id}:visits",
            $this::DEFAULT_CACHE_TTL,
            function () use ($searchByQuery, $id) {
                return ProfilVisit::with([
                    'store',
                    'user',
                ])->whereHas('store', function (EloquentBuilder $query) use ($id) {
                    $query->where('id', $id);
                })->when($searchByQuery, function (EloquentBuilder $query) use ($searchByQuery) {
                    $query->where('user', 'LIKE', '%' . $searchByQuery . '%');
                })->orderBy('user', 'asc')
                    ->paginate($this::DEFAULT_PAGINATE);
            }
        );

        return $this->successResponse(
            statusCode: 200,
            success: true,
            msg: "Successfully fetch store visits.",
            resource: $storeVisitsCache,
        );
    }

    public function getOneVisitData(int $id, int $visitId): JsonResponse
    {
        $storeVisitCache = Cache::remember(
            "stores:{$id}:visits:{$visitId}",
            $this::DEFAULT_CACHE_TTL,
            function () use ($id, $visitId) {
                return ProfilVisit::with([
                    'store',
                    'user',
                ])->whereHas('store', function (EloquentBuilder $query) use ($id) {
                    $query->where('id', $id);
                })->where('id', $visitId)
                    ->firstOrFail();
            }
        );

        return $this->successResponse(
            statusCode: 200,
            success: true,
            msg: "Successfully fetch store visit {$visitId}.",
            resource: $storeVisitCache,
        );
    }

    public function getAllOrdersData(Request $request, int $id): JsonResponse
    {
        $searchByQuery = $request->query('q');

        $storeOrdersCache = Cache::remember(
            "stores:{$id}:orders",
            $this::DEFAULT_CACHE_TTL,
            function () use ($searchByQuery, $id) {
                return OrderCustomerSales::with([
                    'status',
                    'store',
                    'details',
                ])->whereHas('store', function (EloquentBuilder $query) use ($id) {
                    $query->where('id', $id);
                })->when($searchByQuery, function (EloquentBuilder $query) use ($searchByQuery) {
                    $query->where('no_order', 'LIKE', '%' . $searchByQuery . '%');
                })->orderBy('no_order', 'asc')
                    ->paginate($this::DEFAULT_PAGINATE);
            }
        );

        return $this->successResponse(
            statusCode: 200,
            success: true,
            msg: "Successfully fetch store {$id} orders.",
            resource: $storeOrdersCache,
        );
    }

    public function getOneOrderData(int $id, int $orderId): JsonResponse
    {
        $storeOrderCache = Cache::remember("stores:{$id}:orders:{$orderId}", $this::DEFAULT_CACHE_TTL, function () use ($id, $orderId) {
            return OrderCustomerSales::with([
                'status',
                'store',
                'details',
            ])->whereHas('store', function (EloquentBuilder $query) use ($id) {
                $query->where('id', $id);
            })->where('id', $orderId)
                ->firstOrFail();
        });

        return $this->successResponse(
            statusCode: 200,
            success: true,
            msg: "Successfully fetch store {$id} order {$orderId}.",
            resource: $storeOrderCache,
        );
    }

    public function getAllTypesData(Request $request): JsonResponse
    {
        $searchByQuery = $request->query('q');

        $storeTypesCache = Cache::remember('storeTypes', $this::DEFAULT_CACHE_TTL, function () use ($searchByQuery) {
            return StoreType::with('stores')
                ->when($searchByQuery, function (EloquentBuilder $query) use ($searchByQuery) {
                    $query->where('store_type_name', 'LIKE', '%' . $searchByQuery . '%');
                })->orderBy('store_type_name')
                ->paginate($this::DEFAULT_PAGINATE);
        });

        return $this->successResponse(
            statusCode: 200,
            success: true,
            msg: "Successfully fetch store types",
            resource: $storeTypesCache,
        );
    }

    public function getOneTypeData(int $id): JsonResponse
    {
        $storeTypeCache = Cache::remember('storeTypeCache', $this::DEFAULT_CACHE_TTL, function () use ($id) {
            return StoreType::with(['stores'])
                ->where('store_type_id', $id)
                ->firstOrFail();
        });

        return $this->successResponse(
            statusCode: 200,
            success: true,
            msg: "Successfully fetch store types {$id}",
            resource: $storeTypeCache,
        );
    }

    public function storeOneTypeData(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'store_type_name' => ['required', 'string', 'max:255'],
        ]);

        if ($validator->fails()) {
            return $this->clientErrorResponse(
                statusCode: 422,
                success: false,
                msg: $validator->errors()->first(),
            );
        }

        try {
            DB::beginTransaction();

            $storeType = StoreType::create([
                'store_type_name' => $request->store_type_name,
            ]);

            DB::commit();

            return $this->successResponse(
                statusCode: 201,
                success: true,
                msg: "Successfully create new store type.",
                resource: $storeType
            );
        } catch (\Symfony\Component\HttpKernel\Exception\HttpException $e) {
            DB::rollBack();

            return $this->errorResponse(
                statusCode: $e->getStatusCode(),
                success: false,
                msg: $e->getMessage(),
            );
        } catch (\Error $e) {
            DB::rollBack();

            return $this->errorResponse(
                statusCode: 500,
                success: false,
                msg: $e->getMessage(),
            );
        } catch (\Exception $e) {
            DB::rollBack();

            return $this->errorResponse(
                statusCode: 500,
                success: false,
                msg: $e->getMessage(),
            );
        }
    }

    public function updateOneTypeData(Request $request, int $id): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'store_type_name' => ['required', 'string', 'max:255'],
        ]);

        if ($validator->fails()) {
            return $this->clientErrorResponse(
                statusCode: 422,
                success: false,
                msg: $validator->errors()->first(),
            );
        }

        $storeType = StoreType::where('store_type_id', $id)->firstOrFail();

        try {
            DB::beginTransaction();

            $storeType->update([
                'store_type_name' => $request->store_type_name,
            ]);

            DB::commit();

            return $this->successResponse(
                statusCode: 200,
                success: true,
                msg: "Successfully update current store type {$id}.",
            );
        } catch (\Symfony\Component\HttpKernel\Exception\HttpException $e) {
            DB::rollBack();

            return $this->errorResponse(
                statusCode: $e->getStatusCode(),
                success: false,
                msg: $e->getMessage(),
            );
        } catch (\Error $e) {
            DB::rollBack();

            return $this->errorResponse(
                statusCode: 500,
                success: false,
                msg: $e->getMessage(),
            );
        } catch (\Exception $e) {
            DB::rollBack();

            return $this->errorResponse(
                statusCode: 500,
                success: false,
                msg: $e->getMessage(),
            );
        }
    }

    public function removeOneTypeData(int $id): JsonResponse
    {
        $storeType = StoreType::where('store_type_id', $id)->firstOrFail();

        $storeType->delete();

        return $this->successResponse(
            statusCode: 200,
            success: true,
            msg: "Successfully remove current store type {$id}.",
        );
    }

    public function getAllCabangsData(Request $request): JsonResponse
    {
        $searchByQuery = $request->query('q');

        $storeCabangsCache = Cache::remember('storeCabangs', $this::DEFAULT_CACHE_TTL, function () use ($searchByQuery) {
            return StoreCabang::with(['province', 'stores'])
                ->when($searchByQuery, function (EloquentBuilder $query) use ($searchByQuery) {
                    $query->where('nama_cabang', 'LIKE', '%' . $searchByQuery . '%');
                })->orderBy('nama_cabang')->paginate($this::DEFAULT_PAGINATE);
        });

        return $this->successResponse(
            statusCode: 200,
            success: true,
            msg: "Successfully fetch store cabang",
            resource: $storeCabangsCache,
        );
    }

    public function getOneCabangData(int $id): JsonResponse
    {
        $storeCabangCache = Cache::remember('storeCabang', $this::DEFAULT_CACHE_TTL, function () use ($id) {
            return StoreCabang::with(['province', 'stores'])
                ->where('id', $id)
                ->firstOrFail();
        });

        return $this->successResponse(
            statusCode: 200,
            success: true,
            msg: "Successfully fetch store cabang {$id}",
            resource: $storeCabangCache,
        );
    }

    public function storeOneCabangData(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'province_id' => ['required', 'integer'],
            'kode_cabang' => ['required', 'string', 'max:10'],
            'nama_cabang' => ['required', 'string', 'max:200'],
        ]);

        if ($validator->fails()) {
            return $this->clientErrorResponse(
                statusCode: 422,
                success: false,
                msg: $validator->errors()->first(),
            );
        }

        try {
            DB::beginTransaction();

            $storeCabang = StoreCabang::create([
                'province_id' => $request->province_id,
                'kode_cabang' => $request->kode_cabang,
                'nama_cabang' => $request->nama_cabang,
            ]);

            DB::commit();

            return $this->successResponse(
                statusCode: 201,
                success: true,
                msg: "Successfully create new store cabang.",
                resource: $storeCabang
            );
        } catch (\Symfony\Component\HttpKernel\Exception\HttpException $e) {
            DB::rollBack();

            return $this->errorResponse(
                statusCode: $e->getStatusCode(),
                success: false,
                msg: $e->getMessage(),
            );
        } catch (\Error $e) {
            DB::rollBack();

            return $this->errorResponse(
                statusCode: 500,
                success: false,
                msg: $e->getMessage(),
            );
        } catch (\Exception $e) {
            DB::rollBack();

            return $this->errorResponse(
                statusCode: 500,
                success: false,
                msg: $e->getMessage(),
            );
        }
    }

    public function updateOneCabangData(Request $request, int $id): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'province_id' => ['required', 'integer'],
            'kode_cabang' => ['required', 'string', 'max:10'],
            'nama_cabang' => ['required', 'string', 'max:200'],
        ]);

        if ($validator->fails()) {
            return $this->clientErrorResponse(
                statusCode: 422,
                success: false,
                msg: $validator->errors()->first(),
            );
        }

        $storeCabang = StoreCabang::where('id', $id)->firstOrFail();

        try {
            DB::beginTransaction();

            $storeCabang->update([
                'province_id' => $request->province_id,
                'kode_cabang' => $request->kode_cabang,
                'nama_cabang' => $request->nama_cabang,
            ]);

            DB::commit();

            return $this->successResponse(
                statusCode: 200,
                success: true,
                msg: "Successfully update current store cabang {$id}."
            );
        } catch (\Symfony\Component\HttpKernel\Exception\HttpException $e) {
            DB::rollBack();

            return $this->errorResponse(
                statusCode: $e->getStatusCode(),
                success: false,
                msg: $e->getMessage(),
            );
        } catch (\Error $e) {
            DB::rollBack();

            return $this->errorResponse(
                statusCode: 500,
                success: false,
                msg: $e->getMessage(),
            );
        } catch (\Exception $e) {
            DB::rollBack();

            return $this->errorResponse(
                statusCode: 500,
                success: false,
                msg: $e->getMessage(),
            );
        }
    }

    public function removeOneCabangData(int $id): JsonResponse
    {
        $storeCabang = StoreCabang::findOrFail($id);

        $storeCabang->delete();

        return $this->successResponse(
            statusCode: 200,
            success: true,
            msg: "Successfully remove current store cabang {$id}."
        );
    }
}
