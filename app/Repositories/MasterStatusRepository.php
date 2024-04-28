<?php

namespace App\Repositories;

use App\Models\MasterStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class MasterStatusRepository extends Repository implements MasterStatusInterface
{
    public function getAll(): JsonResponse
    {
        $masterStatusCache = Cache::remember(
            'masterStatus', 
            $this::DEFAULT_CACHE_TTL, 
            function () 
        {
            return MasterStatus::with(['orders', 'orderDetails'])
            ->whereHas('orders')
            ->orderBy('id')
            ->paginate($this::DEFAULT_PAGINATE);
        });

        return $this->successResponse(
            statusCode: 200, 
            success: true, 
            msg: "Successfully fetch master status.", 
            resource: $masterStatusCache,
        );
    }

    public function getAllByQuery(Request $request): JsonResponse
    {
        $searchByQuery = $request->query('q');

        $masterStatusByQueryCache = Cache::remember(
            'masterStatusByQuery', 
            $this::DEFAULT_CACHE_TTL,
            function () use ($searchByQuery) 
        {
            return MasterStatus::with([
                'orders', 
                'orderDetails'
            ])
            ->when($searchByQuery, function (Builder $query) use ($searchByQuery) {
                $query->whereHas('orders', function (Builder $subQuery) use ($searchByQuery) {
                    $subQuery->where('no_order', 'LIKE', '%' . $searchByQuery . '%')
                    ->orWhere('cust_code', 'LIKE', '%' . $searchByQuery . '%')
                    ->orWhere('ship_code', 'LIKE', '%' . $searchByQuery . '%')
                    ->orWhere('whs_code', 'LIKE', '%' . $searchByQuery . '%')
                    ->orWhere('whs_code_to', 'LIKE', '%' . $searchByQuery . '%');
                });
            })
            ->orderBy('id', 'asc')
            ->paginate($this::DEFAULT_PAGINATE);
        });

        return $this->successResponse(
            statusCode: 200, 
            success: true, 
            msg: "Successfully fetch master status with query %{$searchByQuery}%.", 
            resource: $masterStatusByQueryCache,
        );
    }

    public function getAllByOrderDateFilter(Request $request): JsonResponse
    {
        $filterByDate = $request->query('q');

        $masterStatusByOrderDateCache = Cache::remember('masterStatusByOrderDate', $this::DEFAULT_CACHE_TTL, function () use ($filterByDate) {
            return MasterStatus::with(['orders', 'orderDetails'])
            ->when($filterByDate, function (Builder $query) use ($filterByDate) {
                $query->whereHas('orders', function (Builder $subQuery) use ($filterByDate) {
                    $subQuery->whereDate('tgl_order', '=', $filterByDate);
                });
            })
            ->orderBy('id', 'asc')
            ->paginate($this::DEFAULT_PAGINATE);
        });

        return $this->successResponse(
            statusCode: 200, 
            success: true, 
            msg: "Successfully fetch master status with order date filter '{$filterByDate}'.", 
            resource: $masterStatusByOrderDateCache,
        );
    }

    public function getOne(int $id): JsonResponse
    {
        $masterStatusCache = Cache::remember("masterStatus:{$id}", $this::DEFAULT_CACHE_TTL, function () use ($id) {
            return MasterStatus::with(['orders', 'orderDetails'])
            ->where('id', $id)
            ->firstOrFail();
        });

        return $this->successResponse(
            statusCode: 200, 
            success: true, 
            msg: "Successfully fetch master status {$id}.", 
            resource: $masterStatusCache,
        );
    }
}