<?php

namespace App\Repositories;

use App\Models\MasterTargetNoo;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class MasterTargetNooRepository extends Repository implements MasterTargetNooInterface
{
    public function getAll(): JsonResponse
    {
        $masterTargetNooCache = Cache::remember(
            'masterTargetNoo', 
            $this::DEFAULT_CACHE_TTL, 
            function() 
        {
            return MasterTargetNoo::orderBy('id')
            ->paginate($this::DEFAULT_PAGINATE);
        });

        return $this->successResponse(
            statusCode: 200, 
            success: true, 
            msg: "Successfully fetch master target noo.", 
            resource: $masterTargetNooCache,
        );
    }

    public function getAllByQuery(Request $request): JsonResponse
    {
        $searchByQuery = $request->query('q');

        $masterTargetNooByQueryCache = Cache::remember(
            'masterTargetNooByQuery', 
            $this::DEFAULT_CACHE_TTL, 
            function() use ($searchByQuery) 
        {
            return MasterTargetNoo::when($searchByQuery, function(Builder $query) use ($searchByQuery) {
                $query->where('usernumber', 'LIKE', '%' . $searchByQuery . '%');
            })
            ->orderBy('id')
            ->paginate($this::DEFAULT_PAGINATE);
        });

        return $this->successResponse(
            statusCode: 200, 
            success: true, 
            msg: "Successfully fetch master target noo with query %{$searchByQuery}%.", 
            resource: $masterTargetNooByQueryCache,
        );
    }

    public function getAllByYearFilter(Request $request): JsonResponse
    {
        $searchByMonthOrYearQuery = $request->query('q');

        $filterByYear = $this->dateRangeFilter->parseYear($searchByMonthOrYearQuery);

        $masterTargetNooByDateFilterCache = Cache::remember(
            'masterTargetNooByDateFilter', 
            $this::DEFAULT_CACHE_TTL, 
            function() use ($filterByYear) 
        {
            if ($filterByYear) {
                return MasterTargetNoo::when($filterByYear, function(Builder $query) use ($filterByYear) {
                    $query->whereBetween('year', $filterByYear);
                })
                ->orderBy('id', 'asc')
                ->paginate($this::DEFAULT_PAGINATE);
            }
        });

        return $this->successResponse(
            statusCode: 200, 
            success: true, 
            msg: "Successfully fetch master target noo with date filter.", 
            resource: $masterTargetNooByDateFilterCache,
        );
    }

    public function getOne(int $id): JsonResponse
    {
        $masterTargetNooCache = Cache::remember("masterTargetNoo:{$id}", $this::DEFAULT_CACHE_TTL, function () use ($id) {
            return MasterTargetNoo::where('id', $id)
            ->firstOrFail();
        });

        return $this->successResponse(
            statusCode: 200, 
            success: true, 
            msg: "Successfully fetch master call plan {$id}.", 
            resource: $masterTargetNooCache,
        );
    }
}