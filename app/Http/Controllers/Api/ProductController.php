<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function getAllData(): JsonResponse
    {
        return $this->productInfoInterface->getAll();
    }

    public function getAllDataByQuery(Request $request): JsonResponse
    {
        return $this->productInfoInterface->getAllByQuery($request);
    }

    public function getOneData(string $prodNumber): JsonResponse
    {
        return $this->productInfoInterface->getOne($prodNumber);
    }
}
