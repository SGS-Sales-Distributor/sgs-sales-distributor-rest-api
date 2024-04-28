<?php

namespace App\Repositories;

use App\Models\MasterTypeProgram;
use App\Models\Program;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class ProgramRepository extends Repository implements ProgramInterface
{
    public function getAll(): JsonResponse
    {
        $programCache =  Cache::remember('program', $this::DEFAULT_CACHE_TTL, function () {
            return Program::with(['masterTypeProgram', 'details'])
            ->orderBy('id', 'asc')
            ->paginate($this::DEFAULT_PAGINATE);
        });

        return $this->successResponse(
            statusCode: 200, 
            success: true, 
            msg: "Successfully fetch program.", 
            resource: $programCache,
        );
    }

    public function getAllByQuery(Request $request): JsonResponse
    {
        $searchByQuery = $request->query('q');

        $programByQueryCache = Cache::remember('programByQuery', $this::DEFAULT_CACHE_TTL, function () use ($searchByQuery) {
            return Program::with(['masterTypeProgram', 'details'])
            ->when($searchByQuery, function (Builder $query) use ($searchByQuery) {
                $query->where('name_program', 'LIKE', '%' . $searchByQuery . '%')
                ->orWhereHas('masterTypeProgram', function (Builder $query) use ($searchByQuery) {
                    $query->where('type', 'LIKE', '%' . $searchByQuery .'%');
                });
            })
            ->orderBy('id', 'asc')
            ->paginate($this::DEFAULT_PAGINATE);
        });

        return $this->successResponse(
            statusCode: 200, 
            success: true, 
            msg: "Successfully fetch program with query %{$searchByQuery}%.", 
            resource: $programByQueryCache,
        );
    }

    public function getAllByPeriodeFilter(Request $request): JsonResponse
    {
        $filterByPerStart = Carbon::parse($request->query('start-date'));

        $filterByPerEnd = Carbon::parse($request->query('end-date'));

        $programByPeriodeFilterCache = Cache::remember('programByPeriodeFilter', $this::DEFAULT_CACHE_TTL, function () use ($filterByPerStart, $filterByPerEnd) {
            return Program::with(['masterTypeProgram', 'details'])
            ->when($filterByPerStart and $filterByPerEnd, function (Builder $query) use ($filterByPerStart, $filterByPerEnd) {
                $query->whereBetween('periode_start', [$filterByPerStart, $filterByPerEnd])
                ->whereBetween('periode_end', [$filterByPerStart, $filterByPerEnd]);
            })
            ->orderBy('id_type', 'asc')
            ->paginate($this::DEFAULT_PAGINATE);
        });

        return $this->successResponse(
            statusCode: 200, 
            success: true, 
            msg: "Successfully fetch master type program with filter '{$filterByPerStart}' and '{$filterByPerEnd}'.", 
            resource: $programByPeriodeFilterCache,
        );
    }

    public function getOne(int $id): JsonResponse
    {
        $programCache = Cache::remember("program:{$id}", $this::DEFAULT_CACHE_TTL, function () use ($id) {
            return Program::with(['masterTypeProgram', 'details'])
            ->where('id', $id)
            ->firstOrFail();
        });

        return $this->successResponse(
            statusCode: 200, 
            success: true, 
            msg: "Successfully fetch program {$id}.", 
            resource: $programCache,
        );
    }
}