<?php

namespace App\Repositories;

use App\Models\StoreInfoDistri;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class StoreRepository extends Repository implements StoreInterface
{
    public function getAll(): JsonResponse
    {
        $stores = DB::table('store_info_distri')
        ->select(
            'store_info_distri.store_id AS id_toko',
            'store_info_distri.store_name AS nama_toko',
            'store_info_distri.store_alias AS nama_alias_toko',
            'store_info_distri.store_address AS alamat_toko',
            'store_info_distri.store_phone AS nomor_telepon_toko',
            'store_info_distri.store_fax AS nomor_fax_toko',
            'store_info_distri.store_code AS kode_toko',
            'store_info_distri.active AS status_toko',
            'profil_visit.user AS nama_sales',
            'profil_visit.photo_visit AS foto_check_in',
            'profil_visit.photo_visit_out AS foto_check_out',
            'profil_visit.tanggal_visit AS tanggal_visit',
            'profil_visit.time_in AS waktu_check_in',
            'profil_visit.time_out AS waktu_check_out',
            'profil_visit.lat_in AS latitude_check_in',
            'profil_visit.long_in AS longitude_check_in',
            'profil_visit.lat_out AS latitude_check_out',
            'profil_visit.long_out AS longitude_check_out',
            'profil_visit.approval AS status_approval',
        )
        ->leftJoin('profil_visit', function (JoinClause $join) {
            $join->on('profil_visit.store_id', '=', 'store_info_distri.store_id');
        })
        ->orderBy('store_info_distri.store_id', 'asc')
        ->paginate($this::DEFAULT_PAGINATE);

        return $this->successResponse(
            statusCode: 200, 
            success: true, 
            msg: "Successfully fetch store.", 
            resource: $stores,
    );
    }

    public function getAllOwners(): JsonResponse
    {
        $storeOwnersCache = Cache::remember(
            'storeOwners', 
            $this::DEFAULT_CACHE_TTL, 
            function () 
        {
            return DB::table('store_info_distri')
            ->select('store_info_distri.*', 'store_info_distri_owner.*')
            ->join('store_info_distri_owner', 'store_info_distri.store_id', '=', 'store_info_distri_owner.store_id')
            ->orderBy('store_info_distri.store_id', 'asc')
            ->paginate($this::DEFAULT_PAGINATE);
        });

        return $this->successResponse(
            statusCode: 200, 
            success: true, 
            msg: "Successfully fetch store owners.", 
            resource: $storeOwnersCache,
        );
    }

    public function getAllVisits(int $id): JsonResponse
    {
        $storeVisitsCache = Cache::remember(
            'storeVisits', 
            $this::DEFAULT_CACHE_TTL, 
            function () use ($id) 
        {
            return DB::table('store_info_distri')
            ->select('store_info_distri.*', 'profil_visit.*')
            ->join('profil_visit', 'store_info_distri.store_id', '=' , 'profil_visit.store_id')
            ->where('store_info_distri.store_id', $id)
            ->orderBy('store_info_distri.store_id', 'asc')
            ->paginate($this::DEFAULT_PAGINATE);
        });

        return $this->successResponse(
            statusCode: 200, 
            success: true, 
            msg: "Successfully fetch store visits.", 
            resource: $storeVisitsCache,
        );
    }

    public function getAllByQuery(Request $request): JsonResponse
    {
        $searchByQuery = $request->query('q');

        $storeInfoDistriByQueryCache = Cache::remember(
            'storeInfoDistriByQuery', 
            $this::DEFAULT_CACHE_TTL, 
            function () use ($searchByQuery) 
        {
            return StoreInfoDistri::with([
                'type', 
                'cabang', 
                'visits', 
                'owners', 
                'orders',
                'orderDetails',
                'masterCallPlanDetails'
            ])
            ->when($searchByQuery, function (EloquentBuilder $query) use ($searchByQuery) {
                $query->where('store_name', 'LIKE', '%' . $searchByQuery . '%')
                ->orWhere('store_alias', 'LIKE', '%' . $searchByQuery . '%')
                ->orWhere('store_phone', 'LIKE', '%' . $searchByQuery . '%')
                ->orWhere('store_code', 'LIKE', '%' . $searchByQuery . '%')
                ->orWhereHas('cabang', function (EloquentBuilder $subQuery) use ($searchByQuery) {
                    $subQuery->where('kode_cabang', 'LIKE', '%' . $searchByQuery . '%')
                    ->orWhere('store_cabang.kode_cabang', 'LIKE', '%' . $searchByQuery . '%');
                })
                ->orWhereHas('owners', function (EloquentBuilder $subQuery) use ($searchByQuery) {
                    $subQuery->where('owner', 'LIKE', '%' . $searchByQuery . '%')
                    ->orWhere('nik_owner', 'LIKE', '%' . $searchByQuery . '%')
                    ->orWhere('email_owner', 'LIKE', '%' . $searchByQuery . '%');
                })
                ->orWhereHas('orders', function (EloquentBuilder $subQuery) use ($searchByQuery) {
                    $subQuery->where('no_order', 'LIKE', '%' . $searchByQuery . '%')
                    ->orWhere('cust_code', 'LIKE', '%' . $searchByQuery . '%')
                    ->orWhere('ship_code', 'LIKE', '%' . $searchByQuery . '%')
                    ->orWhere('whs_code', 'LIKE', '%' . $searchByQuery . '%')
                    ->orWhere('whs_code_to', 'LIKE', '%' . $searchByQuery . '%');
                });
            })
            ->orderBy('store_id', 'asc')
            ->paginate($this::DEFAULT_PAGINATE);
        });

        return $this->successResponse(
            statusCode: 200, 
            success: true, 
            msg: "Successfully fetch store with query %{$searchByQuery}%.", 
            resource: $storeInfoDistriByQueryCache,
        );
    }

    public function getAllByOrderDateFilter(Request $request): JsonResponse
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

    public function getOne(int $id): JsonResponse
    {
        $store = DB::table('store_info_distri')
        ->select(
            'store_info_distri.store_id AS id_toko',
            'store_info_distri.store_name AS nama_toko',
            'store_info_distri.store_alias AS nama_alias_toko',
            'store_info_distri.store_address AS alamat_toko',
            'store_info_distri.store_phone AS nomor_telepon_toko',
            'store_info_distri.store_fax AS nomor_fax_toko',
            'store_info_distri.store_code AS kode_toko',
            'store_info_distri.active AS status_toko',
            'store_type.store_type_name AS tipe_toko',
            'store_cabang.kode_cabang AS kode_cabang',
            'store_cabang.nama_cabang AS cabang',
            'store_info_distri_person.owner AS nama_pemilik_toko',
            'store_info_distri_person.nik_owner AS nik_pemilik_toko',
            'store_info_distri_person.email_owner AS email_pemilik_toko',
            'profil_visit.id AS visit_id',
        )
        ->join('store_type', function (JoinClause $join) {
            $join->on('store_info_distri.store_type_id', '=', 'store_type.store_type_id');
        })
        ->join('store_cabang', function (JoinClause $join) {
            $join->on('store_info_distri.subcabang_id', '=', 'store_cabang.id');
        })
        ->leftJoin('store_info_distri_person', function (JoinClause $join) {
            $join->on('store_info_distri_person.store_id', '=', 'store_info_distri.store_id');
        })
        ->leftJoin('profil_visit', function (JoinClause $join) {
            $join->on('profil_visit.store_id', '=', 'store_info_distri.store_id');
        })
        ->where('store_info_distri.store_id', $id)
        ->first();

        return $this->successResponse(
            statusCode: 200, 
            success: true, 
            msg: "Successfully fetch store {$id}.", 
            resource: $store,
        );
    }

    public function getOneOwner(int $ownerId): JsonResponse
    {
        $storeOwnerCache = Cache::remember("store:owner:{$ownerId}", $this::DEFAULT_CACHE_TTL, function () use ($ownerId) {
            return StoreInfoDistri::with([
                'owners'
            ])
            ->whereHas('owners', function (EloquentBuilder $query) use ($ownerId) {
                $query->where('id', $ownerId);
            })
            ->orderBy('store_id', 'asc')
            ->firstOrFail();
        });

        return $this->successResponse(
            statusCode: 200, 
            success: true, 
            msg: "Successfully fetch store owner {$ownerId}.", 
            resource: $storeOwnerCache,
        );
    }

    public function getOneVisit(int $visitId): JsonResponse
    {
        $storeVisitCache = Cache::remember(
            "store:visit:{$visitId}", 
            $this::DEFAULT_CACHE_TTL, 
            function () use ($visitId) 
            {
                return StoreInfoDistri::with([
                    'visits'
                ])
                ->whereHas('visits', function (EloquentBuilder $query) use ($visitId) {
                    $query->where('id', $visitId);
                })
                ->orderBy('store_id', 'asc')
                ->firstOrFail();
            });

            return $this->successResponse(
                statusCode: 200, 
                success: true, 
                msg: "Successfully fetch store visit {$visitId}.", 
                resource: $storeVisitCache,
            );
    }
}