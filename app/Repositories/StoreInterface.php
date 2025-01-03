<?php

namespace App\Repositories;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

interface StoreInterface
{
    public function getAllData(Request $request): JsonResponse;

    public function getAllDataWithoutCallPlans(Request $request): JsonResponse;

    public function getAllDataByOrderDateFilter(Request $request): JsonResponse;

    public function getOneData(Request $request,int $id): JsonResponse;

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

    public function showDataStoreInfoDist(Request $request, int $id): JsonResponse;

    public function getStoreByCbg(Request $request, int $idCab) : JsonResponse ;

    public function saveDraft(Request $request) : JsonResponse ;

    public function changeDraftToDeliv(Request $request) : JsonResponse ;

    public function updateDetail(Request $request, int $id) : JsonResponse ;

    public function deleteDraft(int $id): JsonResponse;
    
    public function getStoresByUsers(Request $request, int $userId) : JsonResponse ;
}
