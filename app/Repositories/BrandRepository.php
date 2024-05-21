<?php

namespace App\Repositories;

use App\Models\Brand;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;

class BrandRepository extends Repository implements BrandInterface
{
    public function getAllData(Request $request): JsonResponse
    {
        $searchByQuery = $request->query('q');
        
        $brandsCache = Cache::remember(
            'brandsCache', 
            $this::DEFAULT_CACHE_TTL, 
            function () use ($searchByQuery) 
        {
            return Brand::with('brandGroup', 'products')
            ->when($searchByQuery, function (Builder $query) use ($searchByQuery) {
                $query->where('brand_name', 'LIKE', '%' . $searchByQuery . '%');
            })
            ->where('status', 1)
            ->orderBy('brand_id', 'asc')
            ->paginate($this::DEFAULT_PAGINATE);
        });

        return $this->successResponse(
            statusCode: 200,
            success: true,
            msg: "Successfully fetch brands.",
            resource: $brandsCache,
        );
    }

    public function getOneData(string $id): JsonResponse
    {
        $brandCache = Cache::remember(
            "brand:{$id}", 
            function () use ($id) 
        {
            return Brand::with('brandGroup', 'products')
            ->where('brand_id', $id)
            ->firstOrFail();
        });

        return $this->successResponse(
            statusCode: 200,
            success: true,
            msg: "Successfully fetch brand {$id}.",
            resource: $brandCache,
        );
    }
}