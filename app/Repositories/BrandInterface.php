<?php

namespace App\Repositories;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

interface BrandInterface
{
    public function getAllData(Request $request): JsonResponse;

    public function getOneData(string $id): JsonResponse;

    public function getAllProductsData(Request $request, string $id): JsonResponse;

    public function getOneProductData(string $id, string $productNumber): JsonResponse;

    public function storeOneData(Request $request): JsonResponse;

    public function updateOneData(Request $request, string $id): JsonResponse;

    public function removeOneData(string $id): JsonResponse;

    public function getAllGroupsData(Request $request): JsonResponse;

    public function getOneGroupData(int $id): JsonResponse;

    public function storeOneGroupData(Request $request): JsonResponse;

    public function updateOneGroupData(Request $request, int $id): JsonResponse;

    public function removeOneGroupData(int $id): JsonResponse;
}