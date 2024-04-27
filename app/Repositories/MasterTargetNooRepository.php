<?php

namespace App\Repositories;

use App\Models\MasterTargetNoo;
use Illuminate\Database\Query\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class MasterTargetNooRepository extends Repository implements MasterTargetNooInterface
{
    public function getAll(): JsonResponse
    {
        $masterTargetNooCache = Cache::remember('masterTargetNoo', $this::DEFAULT_CACHE_TTL, function() {
            return DB::table('master_target_noo')
            ->select('*')
            ->orderBy('id')
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
        $searchByQuery = $request->query('query');

        $masterTargetNooByQueryCache = Cache::remember('masterTargetNooByQuery', $this::DEFAULT_CACHE_TTL, function() use ($searchByQuery) {
            return DB::table('master_target_noo')
            ->when($searchByQuery, function(Builder $query) use ($searchByQuery) {
                $query->where('usernumber', 'LIKE', '%' . $searchByQuery . '%');
            })
            ->select('*')
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

    public function getAllByDateFilter(Request $request): JsonResponse
    {
        $filterByMonth = $request->input('month');

        $filterByYear = $request->input('year');

        $masterTargetNooByDateFilterCache = Cache::remember('masterTargetNooByDateFilter', $this::DEFAULT_CACHE_TTL, function() use ($filterByMonth, $filterByYear) {
            if ($filterByMonth) {
                return DB::table('master_target_noo')
                ->when($filterByMonth, function(Builder $query) use ($filterByMonth) {
                    $query->where('month', '=', $filterByMonth);
                })
                ->select('*')
                ->orderBy('id', 'asc')
                ->paginate($this::DEFAULT_PAGINATE);
            }

            if ($filterByYear) {
                return DB::table('master_target_noo')
                ->when($filterByYear, function(Builder $query) use ($filterByYear) {
                    $query->where('year', '=', $filterByYear);
                })
                ->select('*')
                ->orderBy('id')
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
        $masterTargetNoo = MasterTargetNoo::where('id', $id)->firstOrFail();

        return $this->successResponse(
            statusCode: 200, 
            success: true, 
            msg: "Successfully fetch master call plan {$id}.", 
            resource: $masterTargetNoo,
        );
    }
}