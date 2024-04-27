<?php

namespace App\Repositories;

use App\Models\StoreInfoDistri;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Query\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class StoreRepository extends Repository implements StoreInterface
{
    public function getAll(): JsonResponse
    {
        $storeInfoDistriCache = Cache::remember('storeInfoDistri', $this::DEFAULT_CACHE_TTL, function () {
            return StoreInfoDistri::with([
                'type', 
                'cabang', 
                'visits', 
                'owners', 
                'orders', 
                'masterCallPlanDetails'
            ])
            ->whereHas('owners')
            ->orderBy('store_id', 'asc')
            ->paginate($this::DEFAULT_PAGINATE);
        });

        return $this->successResponse(
            statusCode: 200, 
            success: true, 
            msg: "Successfully fetch store info distri.", 
            resource: $storeInfoDistriCache,
        );
    }

    public function getAllByQuery(Request $request): JsonResponse
    {
        $searchByQuery = $request->query('query');

        $storeInfoDistriByQueryCache = Cache::remember('storeInfoDistriByQuery', function () use ($searchByQuery) {
            return StoreInfoDistri::with([
                'type', 
                'cabang', 
                'visits', 
                'owners', 
                'orders', 
                'masterCallPlanDetails'
            ])
            ->when($searchByQuery, function (EloquentBuilder $query) use ($searchByQuery) {
                $query->where('store_name', 'LIKE', '%' . $searchByQuery . '%')
                ->orWhere('store_alias', 'LIKE', '%' . $searchByQuery . '%')
                ->orWhere('store_phone', 'LIKE', '%' . $searchByQuery . '%')
                ->orWhere('store_code', 'LIKE', '%' . $searchByQuery . '%')
                ->orWhereHas('store_cabang', function (EloquentBuilder $query) use ($searchByQuery) {
                    $query->where('kode_cabang', 'LIKE', '%' . $searchByQuery . '%')
                    ->orWhere('store_cabang.kode_cabang', 'LIKE', '%' . $searchByQuery . '%');
                })
                ->orWhereHas('store_info_distri_person', function (EloquentBuilder $query) use ($searchByQuery) {
                    $query->where('owner', 'LIKE', '%' . $searchByQuery . '%')
                    ->orWhere('nik_owner', 'LIKE', '%' . $searchByQuery . '%')
                ->orWhere('email_owner', 'LIKE', '%' . $searchByQuery . '%');
                });
            })
            ->whereHas('owners')
            ->orderBy('store_id', 'asc')
            ->paginate($this::DEFAULT_PAGINATE);
        });

        return $this->successResponse(
            statusCode: 200, 
            success: true, 
            msg: "Successfully fetch store info distri with query %{$searchByQuery}%.", 
            resource: $storeInfoDistriByQueryCache,
        );
    }

    public function getAllByTypeFilter(Request $request): JsonResponse
    {
        $filterByType = $request->input('type');

        $storeInfoDistriByTypeFilterCache = Cache::remember('storeInfoDistriByTypeFilter', function () use ($filterByType) {
            return DB::table('store_info_distri')
            ->select('store_info_distri.*', 'store_type.*', 'store_cabang.*', 'profil_visit.*', 'store_info_distri_person.*', 'order_customer_sales.*', 'master_call_plan_detail.*')
            ->join('store_type', 'store_info_distri.store_type_id', '=' , 'store_type.store_type_id')
            ->join('store_cabang', 'store_info_distri.subcabang_id', '=', 'store_cabang.id')
            ->join('profil_visit', 'store_info_distri.store_id', '=', 'profil_visit.store_id')
            ->join('store_info_distri_person', 'store_info_distri.store_id', '=', 'store_info_distri_person.store_id')
            ->join('order_customer_sales', 'store_info_distri.store_id', '=', 'order_customer_sales.store_id')
            ->join('master_call_plan_detail', 'store_info_distri.store_id', '=', 'master_call_plan_detail.store_id')
            ->when($filterByType, function (Builder $query) use ($filterByType) {
                $query->where('store_type.store_type_name', '=', $filterByType);
            })
            ->orderBy('store_info_distri.store_id', 'asc')
            ->paginate($this::DEFAULT_PAGINATE);
        });

        return $this->successResponse(
            statusCode: 200, 
            success: true, 
            msg: "Successfully fetch store info distri with store type filter '{$filterByType}'.", 
            resource: $storeInfoDistriByTypeFilterCache,
        );
    }

    public function getOne(int $id): JsonResponse
    {
        $store = StoreInfoDistri::with([
            'type',
            'cabang',
            'visits', 
            'owners',
            'orders',
            'masterCallPlanDetails', 
        ])
        ->where('store_id', $id)
        ->firstOrFail();

        return $this->successResponse(
            statusCode: 200, 
            success: true, 
            msg: "Successfully fetch store info distri {$id}.", 
            resource: $store,
        );
    }
}