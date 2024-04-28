<?php

namespace App\Repositories;

use App\Models\ProductInfoDo;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class ProductInfoRepository extends Repository implements ProductInfoInterface
{
    public function getAll(): JsonResponse
    {
        $productsCache = Cache::remember('products', $this::DEFAULT_CACHE_TTL, function () {
            return ProductInfoDo::with([
                'status',
                'brand',
                'type',
                'productInfoLmts',
                'dataReturDetails',
            ])
            ->orderBy('prod_number', 'asc')
            ->paginate($this::DEFAULT_PAGINATE);
        });

        return $this->successResponse(
            statusCode: 200, 
            success: true, 
            msg: "Successfully fetch product.", 
            resource: $productsCache,
        );
    }

    public function getAllByQuery(Request $request): JsonResponse
    {
        $searchByQuery = $request->query('q');

        $productsByQuery = Cache::remember('productByQuery', $this::DEFAULT_CACHE_TTL, function () use ($searchByQuery) {
            return ProductInfoDo::with([
                'status',
                'brand',
                'type',
                'productInfoLmts',
                'dataReturDetails',
            ])
            ->when($searchByQuery, function (Builder $query) use ($searchByQuery) {
                $query->where('prod_number', 'LIKE', '%' . $searchByQuery . '%')
                ->orWhere('prod_barcode_number', 'LIKE', '%' . $searchByQuery . '%')
                ->orWhere('prod_name', 'LIKE', '%' . $searchByQuery . '%')
                ->orWhere('prod_universal_number', 'LIKE', '%' . $searchByQuery . '%');
            })
            ->orderBy('prod_number', 'asc')
            ->paginate($this::DEFAULT_PAGINATE);
        }); 

        return $this->successResponse(
            statusCode: 200, 
            success: true, 
            msg: "Successfully fetch product with query %{$searchByQuery}%.", 
            resource: $productsByQuery,
        );
    }

    public function getOne(string $prodNumber): JsonResponse
    {
        $productCache = Cache::remember('product', $this::DEFAULT_CACHE_TTL, function () use ($prodNumber) {
            return ProductInfoDo::with([
                'status',
                'brand',
                'type',
                'productInfoLmts',
                'dataReturDetails',
            ])->where('prod_number', $prodNumber)
            ->firstOrFail();
        });

        return $this->successResponse(
            statusCode: 200, 
            success: true, 
            msg: "Successfully fetch program {$prodNumber}.", 
            resource: $productCache,
        );
    }
}