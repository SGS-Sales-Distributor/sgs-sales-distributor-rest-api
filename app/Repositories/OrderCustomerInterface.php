<?php

namespace App\Repositories;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

interface OrderCustomerInterface
{
    public function getAllData(Request $request): JsonResponse;

    public function getOneData(int $id): JsonResponse;

    public function getAllDetailsData(Request $request, int $id): JsonResponse;

    public function getOneDetailData(int $id, int $detailId): JsonResponse;

    public function storeOneData(Request $request): JsonResponse;

    public function updateOneData(Request $request, int $id): JsonResponse;

    public function removeOneData(int $id): JsonResponse;
}