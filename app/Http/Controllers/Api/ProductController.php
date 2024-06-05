<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ProductInfoDo;
use App\Models\PublicModel;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    public function getAll(Request $request): JsonResponse
    {
        return $this->productInterface->getAllData($request);
    }

    public function getOne(string $productNumber): JsonResponse
    {
        return $this->productInterface->getOneData($productNumber);
    }

    public function storeOne(Request $request): JsonResponse
    {
        return $this->productInterface->storeOneData($request);
    }

    public function updateOne(Request $request, string $productNumber): JsonResponse
    {
        return $this->productInterface->updateOneData($request, $productNumber);
    }

    public function removeOne(string $productNumber): JsonResponse
    {
        return $this->productInterface->removeOneData($productNumber);
    }

    public function getAllBasic(): JsonResponse
    {
        return $this->productInterface->getAllBasic();
    }

    public function getAllBasicWithPaging(Request $request): JsonResponse
    {
        return $this->productInterface->getAllBasicWithPaging($request);
    }

    public function getOneBasic(string $productNumber): JsonResponse
    {
        return $this->productInterface->getOneBasic($productNumber);
    }

    public function storeBasicData(Request $request): JsonResponse
    {
        return $this->productInterface->storeBasicData($request);
    }

    public function updateBasicData(Request $request, string $productNumber): JsonResponse
    {
        return $this->productInterface->updateBasicData($request, $productNumber);
    }

    public function removeBasicData(string $productNumber): JsonResponse
    {
        return $this->productInterface->removeBasicData($productNumber);
    }

    // public function index()
    // {
    //     try {
    //         $todos = ProductInfoDo::with('productInfoLmt')->orderBy('prod_number', 'asc')->paginate(10);
            
    //         return response()->json([
    //             'code' => 200,
    //             'status' => true,
    //             'total' => $todos->total(),
    //             'last_page' => $todos->lastPage(),
    //             'data' => $todos->items(),
    //         ], 200);
    //     } catch (\Exception $e) {
    //         return response()->json([
    //             'code' => 409,
    //             'status' => false,
    //             'message' => 'failed get data',
    //             'error' => $e->getMessage()
    //         ], 409);
    //     }
    // }
}
