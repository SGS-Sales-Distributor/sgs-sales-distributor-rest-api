<?php

namespace App\Repositories;

use App\Models\StoreInfoDistri;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class StoreRepository extends Repository implements StoreInterface
{
    public function getAll(): JsonResponse
    {
        $storeInfoDistriCache = Cache::remember(
            'storeInfoDistri', 
            $this::DEFAULT_CACHE_TTL, 
            function () 
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
            msg: "Successfully fetch store info distri with query %{$searchByQuery}%.", 
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
            msg: "Successfully fetch store info distri with store type filter {$request->input('type')}.", 
            resource: $storeInfoDistriByTypeFilterCache,
        );
    }

    public function getOne(int $id): JsonResponse
    {
        $storeCache = Cache::remember(
            "store:{$id}", 
            $this::DEFAULT_CACHE_TTL, 
            function () use ($id) 
        {
            return StoreInfoDistri::with([
                'type',
                'cabang',
                'visits', 
                'owners',
                'orders',
                'orderDetails',
                'masterCallPlanDetails', 
            ])
            ->where('store_id', $id)
            ->firstOrFail();
        });

        return $this->successResponse(
            statusCode: 200, 
            success: true, 
            msg: "Successfully fetch store info distri {$id}.", 
            resource: $storeCache,
        );
    }
}