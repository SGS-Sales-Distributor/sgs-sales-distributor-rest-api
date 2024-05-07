<?php

namespace App\Repositories;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

interface StoreInterface
{
    public function getAllData(Request $request): JsonResponse;

    public function getOneData(int $id): JsonResponse;
    
    public function getAllDataByOrderDateFilter(Request $request): JsonResponse;
    
    public function getAllOwnersData(Request $request, int $id): JsonResponse;
    
    public function getOneOwnerData(int $id, int $ownerId): JsonResponse;

    public function getAllVisitsData(Request $request, int $id): JsonResponse;
    
    public function getOneVisitData(int $id, int $visitId): JsonResponse;

    public function getAllOrdersData(Request $request, int $id): JsonResponse;

    public function getOneOrderData(int $id, int $orderId): JsonResponse;
}
