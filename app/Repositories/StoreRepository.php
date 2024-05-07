<?php

namespace App\Repositories;

use App\Models\OrderCustomerSales;
use App\Models\ProfilVisit;
use App\Models\StoreInfoDistri;
use App\Models\StoreInfoDistriPerson;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class StoreRepository extends Repository implements StoreInterface
{
    public function getAllData(Request $request): JsonResponse
    {
        $searchByQuery = $request->query('q');
        
        $storesCache = Cache::remember(
            "storesCache", 
            $this::DEFAULT_CACHE_TTL, 
            function () use ($searchByQuery) 
        {
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
            ])
            ->leftJoin('profil_visit', 'profil_visit.store_id', '=', 'store_info_distri.store_id')
            ->when($searchByQuery, function (EloquentBuilder $query) use ($searchByQuery) {
                $query->when('store_name', 'LIKE', '%' . $searchByQuery . '%');
            })->orderBy('store_name', 'asc')
            ->paginate($this::DEFAULT_PAGINATE);
            // return StoreInfoDistri::with([
            //     'type',
            //     'cabang',
            //     'visits',
            //     'owners',
            //     'orders',
            //     'orderDetails',
            //     'masterCallPlanDetails',
            // ])->when($searchByQuery, function (EloquentBuilder $query) use ($searchByQuery) {
            //     $query->when('store_name', 'LIKE', '%' . $searchByQuery . '%');
            // })
            // ->orderBy('store_name', 'asc')
            // ->paginate($this::DEFAULT_PAGINATE);
        });

        return $this->successResponse(
            statusCode: 200, 
            success: true, 
            msg: "Successfully fetch store.", 
            resource: $storesCache,
        );
    }

    public function getAllOwnersData(Request $request, int $id): JsonResponse
    {
        $searchByQuery = $request->query('q');

        $storeOwnersCache = Cache::remember(
            "stores:{$id}:owners", 
            $this::DEFAULT_CACHE_TTL, 
            function () use ($searchByQuery, $id) 
        {
            return StoreInfoDistriPerson::with([
                'store',
            ])->whereHas('store', function (EloquentBuilder $query) use ($id) {
                $query->where('id', $id);
            })->when($searchByQuery, function (EloquentBuilder $query) use ($searchByQuery) {
                $query->where('owner', 'LIKE', '%' . $searchByQuery . '%');
            })->orderBy('owner', 'asc')
            ->paginate($this::DEFAULT_PAGINATE);
        });

        return $this->successResponse(
            statusCode: 200, 
            success: true, 
            msg: "Successfully fetch store owners.", 
            resource: $storeOwnersCache,
        );
    }

    public function getAllVisitsData(Request $request, int $id): JsonResponse
    {
        $searchByQuery = $request->query('q');

        $storeVisitsCache = Cache::remember(
            "stores:{$id}:visits", 
            $this::DEFAULT_CACHE_TTL, 
            function () use ($searchByQuery, $id) 
        {
            return ProfilVisit::with([
                'store',
                'user',
            ])->whereHas('store', function (EloquentBuilder $query) use ($id) {
                $query->where('id', $id);
            })->when($searchByQuery, function (EloquentBuilder $query) use ($searchByQuery) {
                $query->where('user', 'LIKE', '%' . $searchByQuery . '%');
            })->orderBy('user', 'asc')
            ->paginate($this::DEFAULT_PAGINATE);
        });

        return $this->successResponse(
            statusCode: 200, 
            success: true, 
            msg: "Successfully fetch store visits.", 
            resource: $storeVisitsCache,
        );
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
            ) 
        {
            if ($filterByDateRange)
            {
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

            if ($filterByDate)
            {
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
        });

        return $this->successResponse(
            statusCode: 200, 
            success: true, 
            msg: "Successfully fetch store with store type filter {$request->input('type')}.", 
            resource: $storeInfoDistriByTypeFilterCache,
        );
    }

    public function getOneData(int $id): JsonResponse
    {
        $store =  DB::table('store_info_distri')
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
        ->leftJoin('store_info_distri_person', 'store_info_distri_person.store_id', '=', 'store_info_distri.store_id')
        ->leftJoin('profil_visit', 'profil_visit.store_id', '=', 'store_info_distri.store_id')
        ->where('store_info_distri.store_id', $id)
        ->first(); 

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
            function () use ($id, $ownerId) 
        {
            return StoreInfoDistriPerson::with([
                'store'
            ])->whereHas('store', function (EloquentBuilder $query) use ($id) {
                $query->where('id', $id);
            })->where('id', $ownerId)
            ->firstOrFail();
        });

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
            function () use ($id, $visitId) 
        {
            return ProfilVisit::with([
                'store',
                'user',
            ])->whereHas('store', function (EloquentBuilder $query) use ($id) {
                $query->where('id', $id);
            })->where('id', $visitId)
            ->firstOrFail();
        });

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
            function () use ($searchByQuery, $id) 
        {
            return OrderCustomerSales::with([
                'status',
                'store',
                'details',
            ])->whereHas('store', function (EloquentBuilder $query) use ($id) {
                $query->where('id', $id);
            })->when($searchByQuery, function (EloquentBuilder $query) use ($searchByQuery) {
                $query->where('no_order', 'LIKE' , '%' . $searchByQuery . '%');
            })->orderBy('no_order', 'asc')
            ->paginate($this::DEFAULT_PAGINATE);
        });

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
}