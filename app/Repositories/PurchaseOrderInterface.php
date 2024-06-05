<?php

namespace App\Repositories;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

interface PurchaseOrderInterface
{
    public function getAll(Request $request): JsonResponse;

    public function getOne(int $id): JsonResponse;

    public function createNewOrder(Request $request): JsonResponse;

    public function updateRecentOrder(Request $request, int $id): JsonResponse;

    public function removeRecentOrder(int $id): JsonResponse;
}