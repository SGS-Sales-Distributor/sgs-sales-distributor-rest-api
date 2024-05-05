<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

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
}
