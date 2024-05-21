<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class StoreInfoDistriController extends Controller
{
    public function getAll(Request $request): JsonResponse
    {
        return $this->storeInterface->getAllData($request);
    }

    public function getAllWithoutCallPlans(Request $request): JsonResponse
    {
        return $this->storeInterface->getAllDataWithoutCallPlans($request);
    }

    public function getOne(int $id): JsonResponse
    {
        return $this->storeInterface->getOneData($id);
    }

    public function getOneWithoutCallPlans(int $id): JsonResponse
    {
        return $this->storeInterface->getOneDatawithoutCallPlan($id);
    }

    public function storeOne(Request $request): JsonResponse
    {
        return $this->storeInterface->storeOneData($request);
    }

    public function updateOne(Request $request, int $id): JsonResponse
    {
        return $this->storeInterface->updateOneData($request, $id);
    }

    public function removeOne(int $id): JsonResponse
    {
        return $this->storeInterface->removeOneData($id);
    }

    public function getAllByOrderDateFilter(Request $request): JsonResponse
    {
        return $this->storeInterface->getAllDataByOrderDateFilter($request);
    }

    public function getAllVisits(Request $request, int $id): JsonResponse
    {
        return $this->storeInterface->getAllVisitsData($request, $id);
    }

    public function getOneVisit(int $id, int $visitId): JsonResponse
    {
        return $this->storeInterface->getOneVisitData($id, $visitId);
    }

    public function getAllOwners(Request $request, int $id): JsonResponse
    {
        return $this->storeInterface->getAllOwnersData($request, $id);
    }

    public function getOneOwner(int $id, int $ownerId): JsonResponse
    {
        return $this->storeInterface->getOneOwnerData($id, $ownerId);
    }

    public function storeOwner(Request $request, int $id): JsonResponse
    {
        return $this->storeInterface->storeOwnersData($request, $id);
    }

    public function updateOwner(Request $request, int $id, int $ownerId): JsonResponse
    {
        return $this->storeInterface->updateOwnerData($request, $id, $ownerId);
    }

    public function removeOwner(int $id, int $ownerId): JsonResponse
    {
        return $this->storeInterface->removeOwnerData($id, $ownerId);
    }

    public function getAllOrders(Request $request, int $id): JsonResponse
    {
        return $this->storeInterface->getAllOrdersData($request, $id);
    }   

    public function getOneOrder(int $id, int $orderId): JsonResponse
    {
        return $this->storeInterface->getOneOrderData($id, $orderId);
    }

    public function getStoreTypes(Request $request): JsonResponse
    {
        return $this->storeInterface->getStoreTypes($request);
    }

    public function getStoreCabangs(Request $request): JsonResponse
    {
        return $this->storeInterface->getStoreCabangs($request);
    }

    public function sendOtp(Request $request): JsonResponse
    {
        return $this->storeInterface->sendOtp($request);
    }

    public function resendOtp(Request $request): JsonResponse
    {
        return $this->storeInterface->resendOtp($request);
    }

    public function confirmOtp(Request $request): JsonResponse
    {
        return $this->storeInterface->confirmOtp($request);
    }
}
