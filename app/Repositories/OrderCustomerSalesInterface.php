<?php

namespace App\Repositories;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

interface OrderCustomerSalesInterface
{
    public function getAllData(Request $request): JsonResponse;

    public function getOneData(int $id): JsonResponse;

    public function showDetailOrder(Request $request, int $id): JsonResponse;
}