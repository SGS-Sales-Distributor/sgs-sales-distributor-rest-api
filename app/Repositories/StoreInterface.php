<?php

namespace App\Repositories;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

interface StoreInterface
{
    public function getAllData(Request $request): JsonResponse;

    public function getAllDataWithoutCallPlans(Request $request): JsonResponse;

    public function getAllDataByOrderDateFilter(Request $request): JsonResponse;

    public function getOneData(int $id): JsonResponse;

    public function getOneDatawithoutCallPlan(int $id): JsonResponse;

    public function storeOneData(Request $request): JsonResponse;

    public function updateOneData(Request $request, int $id): JsonResponse;

    public function removeOneData(int $id): JsonResponse;

    public function sendOtp(Request $request): JsonResponse;

    public function resendOtp(Request $request): JsonResponse;

    public function confirmOtp(Request $request): JsonResponse;
    
    public function getAllOwnersData(Request $request, int $id): JsonResponse;
    
    public function getOneOwnerData(int $id, int $ownerId): JsonResponse;

    public function storeOneOwnersData(Request $request, int $id): JsonResponse;

    public function updateOneOwnerData(Request $request, int $id, int $ownerId): JsonResponse;

    public function removeOneOwnerData(int $id, int $ownerId): JsonResponse;

    public function getAllVisitsData(Request $request, int $id): JsonResponse;
    
    public function getOneVisitData(int $id, int $visitId): JsonResponse;

    public function getAllOrdersData(Request $request, int $id): JsonResponse;

    public function getOneOrderData(int $id, int $orderId): JsonResponse;

    public function getAllTypesData(Request $request): JsonResponse;

    public function getOneTypeData(int $id): JsonResponse;

    public function storeOneTypeData(Request $request): JsonResponse;

    public function updateOneTypeData(Request $request, int $id): JsonResponse;

    public function removeOneTypeData(int $id): JsonResponse;

    public function getAllCabangsData(Request $request): JsonResponse;

    public function getOneCabangData(int $id): JsonResponse;

    public function storeOneCabangData(Request $request): JsonResponse;

    public function updateOneCabangData(Request $request, int $id): JsonResponse;

    public function removeOneCabangData(int $id): JsonResponse;
}
