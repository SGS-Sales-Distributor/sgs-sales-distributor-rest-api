<?php

namespace App\Repositories;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

interface ProductInterface
{
    public function getAllData(Request $request): JsonResponse;

    public function getOneData(string $productNumber): JsonResponse;

    public function storeOneData(Request $request): JsonResponse;

    public function updateOneData(Request $request, string $productNumber): JsonResponse;

    public function removeOneData(string $productNumber): JsonResponse;

    public function getAllBasic(): JsonResponse;
    
    public function getAllBasicWithPaging(Request $request): JsonResponse;

    public function getOneBasic(string $productNumber): JsonResponse;

    public function storeBasicData(Request $request): JsonResponse;

    public function updateBasicData(Request $request, string $productNumber): JsonResponse;

    public function removeBasicData(string $productNumber): JsonResponse;
}