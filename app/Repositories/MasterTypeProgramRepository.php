<?php

namespace App\Repositories;

use App\Models\MasterTypeProgram;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class MasterTypeProgramRepository extends Repository implements MasterTypeProgramInterface
{
    public function getAll(): JsonResponse
    {
        $masterTypeProgramCache =  Cache::remember('masterTypeProgram', $this::DEFAULT_CACHE_TTL, function () {
            return MasterTypeProgram::with(['programs', 'programDetails'])
            ->whereHas('programDetails')
            ->orderBy('id_type', 'asc')
            ->paginate($this::DEFAULT_PAGINATE);
        });

        return $this->successResponse(
            statusCode: 200, 
            success: true, 
            msg: "Successfully fetch master type program.", 
            resource: $masterTypeProgramCache,
        );
    }

    public function getAllByQuery(Request $request): JsonResponse
    {
        $searchByQuery = $request->query('q');

        $masterTypeProgramByQueryCache = Cache::remember('masterTypeProgramByQuery', $this::DEFAULT_CACHE_TTL, function () use ($searchByQuery) {
            return MasterTypeProgram::with(['programs', 'programDetails'])
            ->when($searchByQuery, function (Builder $query) use ($searchByQuery) {
                $query->where('type', 'LIKE', '%' . $searchByQuery . '%')
                ->orWhereHas('programs', function (Builder $query) use ($searchByQuery) {
                    $query->where('name_program', 'LIKE', '%' . $searchByQuery .'%');
                });
            })
            ->orderBy('id_type', 'asc')
            ->paginate($this::DEFAULT_PAGINATE);
        });

        return $this->successResponse(
            statusCode: 200, 
            success: true, 
            msg: "Successfully fetch master type program with query %{$searchByQuery}%.", 
            resource: $masterTypeProgramByQueryCache,
        );
    }

    public function getAllByPeriodeFilter(Request $request): JsonResponse
    {
        $filterByPerStart = Carbon::parse($request->query('start-date'));

        $filterByPerEnd = Carbon::parse($request->query('end-date'));

        $masterTypeProgramByPeriodeFilterCache = Cache::remember('masterTypeProgramByPeriodeFilter', $this::DEFAULT_CACHE_TTL, function () use ($filterByPerStart, $filterByPerEnd) {
            return MasterTypeProgram::with(['programs', 'programDetails'])
            ->when($filterByPerStart and $filterByPerEnd, function (Builder $query) use ($filterByPerStart, $filterByPerEnd) {
                $query->whereHas('programs', function (Builder $subQuery) use ($filterByPerStart, $filterByPerEnd) {
                    $subQuery->whereBetween('periode_start', [$filterByPerStart, $filterByPerEnd])
                    ->whereBetween('periode_end', [$filterByPerStart, $filterByPerEnd]);
                });
            })
            ->orderBy('id_type', 'asc')
            ->paginate($this::DEFAULT_PAGINATE);
        });

        return $this->successResponse(
            statusCode: 200, 
            success: true, 
            msg: "Successfully fetch master type program with filter '{$filterByPerStart}' and '{$filterByPerEnd}'.", 
            resource: $masterTypeProgramByPeriodeFilterCache,
        );
    }

    public function getOne(int $id): JsonResponse
    {
        $masterTypeProgramCache = Cache::remember("masterTypeProgram:{$id}", $this::DEFAULT_CACHE_TTL, function () use ($id) {
            return MasterTypeProgram::with(['programs', 'programDetails'])
            ->where('id_type', $id)
            ->firstOrFail();
        });

        return $this->successResponse(
            statusCode: 200, 
            success: true, 
            msg: "Successfully fetch master type program {$id}.", 
            resource: $masterTypeProgramCache,
        );
    }
}