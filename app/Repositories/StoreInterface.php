<?php

namespace App\Repositories;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

interface StoreInterface
{
    public function getAllData(Request $request): JsonResponse;

    public function getAllDataByOrderDateFilter(Request $request): JsonResponse;

    public function getOneData(int $id): JsonResponse;

    public function storeOneData(Request $request): JsonResponse;

    public function updateOneData(Request $request, int $id): JsonResponse;

    public function removeOneData(int $id): JsonResponse;
    
    public function getAllOwnersData(Request $request, int $id): JsonResponse;
    
    public function getOneOwnerData(int $id, int $ownerId): JsonResponse;

    public function storeOwnersData(Request $request, int $id): JsonResponse;

    public function updateOwnerData(Request $request, int $id, int $ownerId): JsonResponse;

    public function removeOwnerData(int $id, int $ownerId): JsonResponse;

    public function getAllVisitsData(Request $request, int $id): JsonResponse;
    
    public function getOneVisitData(int $id, int $visitId): JsonResponse;

    public function getAllOrdersData(Request $request, int $id): JsonResponse;

    public function getOneOrderData(int $id, int $orderId): JsonResponse;

    public function getStoreTypes(Request $request): JsonResponse;

    public function getStoreCabangs(Request $request): JsonResponse;

    public function sendOtp(Request $request): JsonResponse;

    public function resendOtp(Request $request): JsonResponse;

    public function confirmOtp(Request $request): JsonResponse;
}
