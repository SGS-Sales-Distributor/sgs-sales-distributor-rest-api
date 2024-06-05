<?php

namespace App\Repositories;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

interface OrderCustomerInterface
{
    public function getAllData(Request $request): JsonResponse;

    public function getOneData(int $id): JsonResponse;
}