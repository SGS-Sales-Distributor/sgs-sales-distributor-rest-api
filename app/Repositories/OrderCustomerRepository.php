<?php

namespace App\Repositories;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;

class OrderCustomerRepository extends Repository implements OrderCustomerInterface
{
    public function getAllData(Request $request): JsonResponse
    {
        $searchByQuery = $request->query('q');

        $orderCustomersCache = Cache::remember('');
    }

    public function getOneData(int $id): JsonResponse
    {
        
    }
}