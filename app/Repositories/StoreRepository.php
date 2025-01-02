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
use Auth;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Query\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use SebastianBergmann\Environment\Console;
use App\Models\User;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\URL;
use App\Models\PublicModel;

class StoreRepository extends Repository implements StoreInterface
{
    public static function str_random($length = 16)
    {
        $pool = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';

        return substr(str_shuffle(str_repeat($pool, 5)), 0, $length);
    }

    public function getAllData(Request $request): JsonResponse
    {
        $searchByQuery = $request->query('q');
        $userId = $request->userId;

        $storesCache = Cache::remember(
            "storesCache",
                $this::DEFAULT_CACHE_TTL,
            function () use ($searchByQuery, $userId) {
                //  DB::enableQueryLog();
                return DB::table('store_info_distri')
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
                        'profil_visit.id as visit_id',
                        'profil_visit.user as nama_salesman',
                        'profil_visit.tanggal_visit as tanggal_visit',
                        'profil_visit.time_in as waktu_masuk',
                        'profil_visit.time_out as waktu_keluar',
                        'profil_visit.ket as keterangan',
                        'profil_visit.approval as approval',
                        'master_call_plan_detail.date',
                    ])
                    ->join('master_call_plan_detail', 'master_call_plan_detail.store_id', '=', 'store_info_distri.store_id')
                    ->where('master_call_plan_detail.date', '=', Carbon::now()->format('Y-m-d'))
                    ->leftJoin('profil_visit', function ($leftJoin) use ($userId) {
                    $leftJoin->on('profil_visit.store_id', '=', 'store_info_distri.store_id')
                        ->on('profil_visit.tanggal_visit', '=', 'master_call_plan_detail.date')
                        ->on('profil_visit.user', '=', DB::raw("'" . $userId . "'"));
                })
                    // ->on('profil_visit .user','=', $userId)
                    ->leftJoin('master_call_plan', function ($leftJoin2) {
                    $leftJoin2->on('master_call_plan.id', '=', 'master_call_plan_detail.call_plan_id');
                })
                    ->where('master_call_plan.user_id', '=', $userId)
                    ->when($searchByQuery, function (Builder $query) use ($searchByQuery) {
                    $query->where('store_info_distri.store_name', 'LIKE', '%' . $searchByQuery . '%');
                })
                    ->orderBy('store_info_distri.store_name', 'asc')
                    ->paginate($this::DEFAULT_PAGINATE);
                // $log = DB::getQueryLog();
                // dd($log);
            }
        );

        return $this->successResponse(
            statusCode: 200,
            success: true,
            msg: "Successfully fetch store",
            resource: $storesCache,
        );
    }


    public function showDataStoreInfoDist(Request $request, int $id): JsonResponse
    {
        // DB::enableQueryLog();
        $storeShowdata = DB::table('store_info_distri')
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
                'store_info_distri_person.owner as owner',
                'store_info_distri_person.nik_owner as nikowner',
                'store_info_distri_person.email_owner as emailOwner',
                'store_info_distri_person.ktp_owner as ktpOwner',
                'store_info_distri_person.photo_other as otherPhoto',

            ])
            ->leftJoin('store_info_distri_person', 'store_info_distri_person.store_id', '=', 'store_info_distri.store_id')
            ->where('store_info_distri.store_id', $id)
            ->first();
        // $log = DB::getQueryLog();
        // dd($log);
        ;

        return $this->successResponse(
            statusCode: 200,
            success: true,
            msg: "Successfully fetch Data Store Info Distri.",
            resource: $storeShowdata,
        );
        // return response()->json($storeShowdata);
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

    public function getAllDataWithoutCallPlans(Request $request): JsonResponse
    {
        $URL = URL::current();
        $searchByQuery = $request->query('search');
        $arr_pagination = (new PublicModel())->paginateDataWithoutSearchQuery($URL, $request->limit, $request->offset);

        // DB::enableQueryLog();
        $storeCallPlansCache = Cache::remember(
            "storeCallPlansCache",
                $this::DEFAULT_CACHE_TTL,
            function () use ($searchByQuery, $arr_pagination) {
                return StoreInfoDistri::with('owners', 'cabang')
                    // ->select(
                    //     'store_id',
                    //     'store_name as nama_toko',
                    //     'store_alias as alias_toko',
                    //     'store_address as alamat_toko',
                    //     'store_phone as nomor_telepon_toko',
                    //     'store_fax as nomor_fax_toko',
                    //     'store_type_id',
                    //     'subcabang_id',
                    //     'store_code as kode_toko',
                    //     'active as status_toko',
                    // )
                    ->when($searchByQuery, function (EloquentBuilder $query) use ($searchByQuery) {
                        $query->where('store_name', 'LIKE', '%' . $searchByQuery . '%');
                        // ->orWhere('kode_cabang', 'LIKE', '%' . $searchByQuery . '%')
                        // ->orWhere('nama_cabang', 'LIKE', '%' . $searchByQuery . '%');
                    })
                    // ->whereHas('owners')
                    ->orderBy('store_name', 'asc')
                    ->limit($arr_pagination['limit'])
                    ->offset($arr_pagination['offset'])
                    ->paginate($this::DEFAULT_PAGINATE);
                // $log = DB::getQueryLog();
                // dd($log);
            }
        );

        return $this->successResponse(
            statusCode: 200,
            success: true,
            msg: "Successfully fetch store based on call plans",
            resource: $storeCallPlansCache,
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

    public function getAllDataByOrderDateFilter(Request $request): JsonResponse
    {
        // $URL = URL::current();
        $searchByOrderDateQuery = $request->query('search');
        // $arr_pagination = (new PublicModel())->paginateDataWithoutSearchQuery($URL, $request->limit, $request->offset);

        $filterByDateRange = $this->dateRangeFilter->parseDateRange($searchByOrderDateQuery);

        $filterByDate = $this->dateRangeFilter->parseDate($searchByOrderDateQuery);

        $storeInfoDistriByTypeFilterCache = Cache::remember(
            'storeInfoDistriByTypeFilter',
                $this::DEFAULT_CACHE_TTL,
            function () use ($filterByDateRange, $filterByDate, ) {
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

    public function getOneData(Request $request, int $id): JsonResponse
    {
        $userId = $request->userId;
        //  DB::enableQueryLog();
        $store = DB::table('master_call_plan_detail')
            ->select([
                'store_info_distri.store_id',
                'store_info_distri.store_name as nama_toko',
                'store_info_distri.store_alias as alias_toko',
                'store_info_distri.store_address as alamat_toko',
                'store_info_distri.store_phone as nomor_telepon_toko',
                'store_info_distri.store_fax as nomor_fax_toko',
                'store_info_distri.store_type_id',
                'store_info_distri.subcabang_id',
                'store_info_distri.active as status_toko',
                'store_info_distri.store_code as kode_toko',
                'store_info_distri_person.owner as nama_pemilik',
                'store_info_distri_person.nik_owner as nik_pemilik',
                'store_info_distri_person.email_owner as email_pemilik',
                'profil_visit.id as visit_id',
                'profil_visit.user as nama_salesman',
                'profil_visit.tanggal_visit as tanggal_visit',
                'profil_visit.time_in as waktu_masuk',
                'profil_visit.time_out as waktu_keluar',
                'profil_visit.ket as keterangan',
                'profil_visit.approval as approval',
            ])
            ->join('store_info_distri', 'store_info_distri.store_id', '=', 'master_call_plan_detail.store_id')
            ->join('master_call_plan', 'master_call_plan.id', '=', 'master_call_plan_detail.call_plan_id')
            ->join('store_info_distri_person', 'store_info_distri_person.store_id', '=', 'store_info_distri.store_id')
            ->leftJoin('profil_visit', function ($leftJoin) {
                $leftJoin->on('profil_visit.tanggal_visit', '=', 'master_call_plan_detail.date')
                    ->on('profil_visit.user', '=', 'master_call_plan.user_id')
                    ->on('profil_visit.store_id', '=', 'master_call_plan_detail.store_id');
            })
            ->where('master_call_plan_detail.store_id', $id)
            ->where('master_call_plan_detail.date', Carbon::now()->format('Y-m-d'))
            ->where('master_call_plan.user_id', '=', DB::raw("'" . $userId . "'"))
            ->first();
        //  $log = DB::getQueryLog();
        //  dd($log);

        return $this->successResponse(
            statusCode: 200,
            success: true,
            msg: "Successfully fetch store {$id}.",
            resource: $store
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

    public function getStoreTypes(Request $request): JsonResponse
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

    public function getStoreCabangs(Request $request): JsonResponse
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
            $lastId = StoreInfoDistri::orderBy('store_id', 'desc')->first()->store_id;
            $setLastId = $lastId + 1;

            $store = StoreInfoDistri::create([
                'store_name' => $request->store_name,
                'store_alias' => $request->store_alias,
                'store_address' => $request->store_address,
                'store_phone' => $request->store_phone,
                'store_fax' => $request->store_fax,
                'store_type_id' => $request->store_type_id,
                'subcabang_id' => $request->subcabang_id,
                'subcabang_idnew' => $request->subcabang_id,
                'store_code' => 'OS' . implode(',', str_split(sprintf('%03d', $request->subcabang_id), 3)) . "-" . sprintf('%04d', $setLastId),
                'active' => 1,
                'created_by' => $request->userNumber,
                'updated_by' => $request->userNumber,
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

    public function storeOwnersData(Request $request, int $id): JsonResponse
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

        $ktp_name = "";

        $photo_other_name = "";

        try {
            if ($request->hasFile('ktp_image') and $request->hasFile('photo_other')) {
                $ktp_image = $request->file('ktp_image');

                $photo_other_image = $request->file('photo_other');

                $ktp_name = date('YmdHis') . '_' . $this->str_random(10) . '.' . 'png';

                $photo_other_name = date('YmdHis') . '_' . $this->str_random(10) . '.' . 'png';

                $ktp_destination_path = public_path('images/ktp');

                $photo_other_destination_path = public_path('images/other');

                $ktp_image->move($ktp_destination_path, $ktp_name);

                $photo_other_image->move($photo_other_destination_path, $photo_other_name);
            }

            DB::beginTransaction();

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

    public function updateOwnerData(Request $request, int $id, int $ownerId): JsonResponse
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

    public function removeOwnerData(int $id, int $ownerId): JsonResponse
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

    public function countPObyStore(int $storeId)
    {
        // DB::enableQueryLog();
        $count = DB::table('order_customer_sales')->
            where('store_id', $storeId)->
            selectRaw('count(id) as jmlPo')
            ->pluck('jmlPo')
            ->first();
        // $log = DB::getQueryLog();
        // dd($log);

        return $count;

    }

    public function storeNameGet(int $storeId)
    {
        $storeInfo = DB::table('store_info_distri')
            ->where('store_id', $storeId)
            ->select('store_name as namaToko')
            ->pluck('namaToko')
            ->first();

        return $storeInfo;
    }

    public function sendOtp(Request $request): JsonResponse
    {
        $store = StoreInfoDistri::where('store_id', $request->idToko)
            ->firstOrFail();

        try {
            DB::beginTransaction();

            $countPo = $this->countPObyStore($request->idToko);
            $storeName = $this->storeNameGet($request->idToko);

            $objectOrder = $request->objOrder;

            $initialNum = 0;

            foreach ($objectOrder as $key => $value) {
                $initialNum += $objectOrder[$key]['qty'];
            }

            //generate NO PO
            $dayPo = date('d');
            $monthPo = date('m');
            $yearP0 = date('Y');
            $generTeNomerPo = $countPo . $dayPo . $monthPo . $yearP0 . $request->idToko;
            $nomor_po = $generTeNomerPo;

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
                'uploaded_by' => $storeName,
                'store_id' => $request->idToko,
                'status_id' => 1,
                'created_by' => $request->userNumber,
                'updated_by' => $request->userNumber,
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
                    'releaseOrder' => 0,
                    'add_disc_1' => 0,
                    'add_disc_2' => $objectOrder[$key]['statusBonus'],
                    'created_by' => $request->userNumber,
                    'updated_by' => $request->userNumber,
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

    protected function generateOTP($id_table, $nomor_po)
    {
        $x = rand(1000, 9999);

        try {
            DB::beginTransaction();

            PurchaseOrderOTP::create([
                'id_po' => $id_table,
                'nomor_po' => $nomor_po,
                'random_otp' => $x,
            ]);

            DB::commit();

            return $x;
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

    public function getStoreByCbg(Request $request, int $idCab): JsonResponse
    {
        // DB::enableQueryLog();
        $store_info_distri = StoreInfoDistri::select([
            'store_id',
            'store_name',
            'store_alias',
            'store_address',
            'store_phone',
            'store_fax',
            'store_type_id',
            'subcabang_id',
            'store_code',
            'active',
            'subcabang_idnew',
            'province_id',
            'kode_cabang',
            'nama_cabang',
            'master_province.province'

            // 'created_by',
            // 'updated_by',
            // 'created_at',
            // 'updated_at',
            // 'deleted_at'
        ])
            ->join('store_cabang', 'store_cabang.id', '=', 'store_info_distri.subcabang_id')
            ->join('master_province', 'master_province.id_province', '=', 'store_cabang.province_id')
            ->where('store_info_distri.subcabang_id', '=', $idCab)
            ->get();
        // $log = DB::getQueryLog();
        // dd($log);

        if (count($store_info_distri) == 0) {
            return $this->errorResponse(
                statusCode: 500,
                success: false,
                msg: 'Data Kosong',
            );
        }


        return $this->successResponse(
            statusCode: 200,
            success: true,
            msg: "Berhasil by cabang yey",
            resource: $store_info_distri,
        );
    }

    public function saveDraft(Request $request): JsonResponse
    {
        $store = StoreInfoDistri::where('store_id', $request->idToko)
            ->firstOrFail();

        try {
            DB::beginTransaction();

            $countPo = $this->countPObyStore($request->idToko);
            $storeName = $this->storeNameGet($request->idToko);

            $objectOrder = $request->objOrder;

            $initialNum = 0;

            foreach ($objectOrder as $key => $value) {
                $initialNum += $objectOrder[$key]['qty'];
            }

            //generate NO PO
            $dayPo = date('d');
            $monthPo = date('m');
            $yearP0 = date('Y');
            $generTeNomerPo = $countPo . $dayPo . $monthPo . $yearP0 . $request->idToko;
            $nomor_po = $generTeNomerPo;

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
                'status_id' => 0,
                'totOrderQty' => $initialNum,
                'totReleaseQty' => 0,
                'keterangan' => $request->metodePembayaran,
                'llb_gabungan_reff' => null,
                'llb_gabungan_sts' => "Open",
                'uploaded_at' => date('Y-m-d H:i:s'),
                'uploaded_by' => $storeName,
                'store_id' => $request->idToko,
                'created_by' => $request->userNumber,
                'updated_by' => $request->userNumber,
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
                    'releaseOrder' => 0,
                    'add_disc_1' => 0,
                    'add_disc_2' => $objectOrder[$key]['statusBonus'],
                    'created_by' => $request->userNumber,
                    'updated_by' => $request->userNumber,
                ]);

                // // get item by item code
                // $item = DB::table('product_info_do AS pid')
                // ->join('stock AS s', 's.prod_number', '=', 'pid.prod_number')
                // ->where('s.prod_number', $objectOrder[$key]['prodNumber'])
                // ->first();

                // // potong stocknya
                // Stock::where('prod_number', $objectOrder[$key]['prodNumber'])
                // ->update(['qty_stock' => ($item->qty_stock - $objectOrder[$key]['qty'])]);
            }

            // $kode_otp = $this->generateOTP($id_table, $nomor_po);

            DB::commit();

            return $this->successResponse(
                statusCode: 201,
                success: true,
                msg: "Pembuatan draft PO berhasil!",
                resource: [
                    'nomor_po' => $nomor_po,
                    //plan detail order ini diremark
                    // 'otp' => $kode_otp,
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

    public function changeDraftToDeliv(Request $request): JsonResponse
    {
        $store = StoreInfoDistri::where('store_id', $request->idToko)
            ->firstOrFail();

        try {
            DB::beginTransaction();

            $objectOrder = $request->objOrder;

            $initialNum = 0;

            foreach ($objectOrder as $key => $value) {
                $initialNum += $objectOrder[$key]['qtyOrder'];
            }

            $id_table = DB::table('order_customer_sales')->where('id', $request->id)->first();
            $nomor_po = $id_table->no_order;

            foreach ($objectOrder as $key => $value) {
                // get item by item code
                $item = DB::table('product_info_do AS pid')
                    ->join('stock AS s', 's.prod_number', '=', 'pid.prod_number')
                    ->where('s.prod_number', $objectOrder[$key]['itemCode'])
                    ->first();

                // potong stocknya
                Stock::where('prod_number', $objectOrder[$key]['itemCode'])
                    ->update(['qty_stock' => ($item->qty_stock - $objectOrder[$key]['qtyOrder'])]);
            }

            $kode_otp = $this->generateOTP($id_table->id, $nomor_po);

            DB::commit();

            return $this->successResponse(
                statusCode: 201,
                success: true,
                msg: "Pembuatan draft PO berhasil!",
                resource: [
                    'nomor_po' => $nomor_po,
                    //plan detail order ini diremark
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

    public function updateDetail(Request $request, int $id): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'prod_name' => 'required|string|max:255',
            'qtyOrder' => 'required|integer|min:1',
            'prod_unit_price' => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation errors',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $detail = OrderCustomerSalesDetail::findOrFail($id);
            $detail->update([
                'prod_name' => $request->prod_name,
                'qtyOrder' => $request->qtyOrder,
                'prod_unit_price' => $request->prod_unit_price,
            ]);

            return response()->json([
                'success' => true,
                'message' => "Detail ID {$id} berhasil diupdate.",
                'resource' => $detail,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function deleteDraft($id): JsonResponse
    {
        Log::info("ID diterima dengan tipe: " . gettype($id));

        try {
            $id = (int) $id;

            $order = OrderCustomerSalesDetail::findOrFail($id);
            $order->delete();

            return response()->json([
                'success' => true,
                'message' => "Draft ID {$id} berhasil dihapus.",
            ], 200);
        } catch (\Exception $e) {
            Log::error("Error saat menghapus draft: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }


    public function getStoresByUsers(Request $request, $userId): JsonResponse
    {
        //  DB::enableQueryLog();
        $storeByUser = DB::table('store_info_distri')
            ->select([
                'store_info_distri.store_id',
                'store_name',
                'store_address',
                'store_fax',
                'store_phone',
                'subcabang_id',
                'store_code',
                'user_info.fullname',
                'user_info.phone',
                'user_info.email',
                'user_info.name',
                'master_province.province',
                'store_cabang.kode_cabang',
                'store_info_distri_person.owner',
                'store_info_distri_person.nik_owner',
                'store_info_distri_person.email_owner',
                'store_cabang.created_by as created_by',
                'store_cabang.updated_by as updated_by',
                'store_cabang.created_at as created_at',
                'store_cabang.updated_at as updated_at',
                'store_cabang.deleted_at as deleted_at',
            ])
            ->join('store_info_distri_person','store_info_distri_person.store_id','=','store_info_distri.store_id')
            ->join('store_cabang', 'store_info_distri.subcabang_id', '=', 'store_cabang.id')
            ->join('user_info', 'user_info.cabang_id', '=', 'store_cabang.id')
            ->join('master_province', 'master_province.id_province', '=', 'store_cabang.province_id')
            ->where('user_info.user_id', '=', $userId)
            ->get();
        // $log = DB::getQueryLog();
        // dd($log);


        return $this->successResponse(
            statusCode: 200,
            success: true,
            msg: "Successfully fetch store By User",
            resource: $storeByUser,
        );
    }
}
