<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class StoreController extends Controller
{
    public function getAllWithoutCallPlans(Request $request): JsonResponse
    {
        return $this->storeInterface->getAllDataWithoutCallPlans($request);
    }

    public function getOneWithoutCallPlan($id): JsonResponse
    {
        return $this->storeInterface->getOneDataWithoutCallPlan($id);
    }

    public function getAll(Request $request): JsonResponse
    {
        return $this->storeInterface->getAllData($request);
    }

    public function getOne(int $id): JsonResponse
    {
        return $this->storeInterface->getOneData($id);
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

    public function storeOneOwner(Request $request, int $id): JsonResponse
    {
        return $this->storeInterface->storeOneOwnersData($request, $id);
    }

    public function updateOneOwner(Request $request, int $id, int $ownerId): JsonResponse
    {
        return $this->storeInterface->updateOneOwnerData($request, $id, $ownerId);
    }

    public function removeOneOwner(int $id, int $ownerId): JsonResponse
    {
        return $this->storeInterface->removeOneOwnerData($id, $ownerId);
    }

    public function getAllOrders(Request $request, int $id): JsonResponse
    {
        return $this->storeInterface->getAllOrdersData($request, $id);
    }

    public function getOneOrder(int $id, int $orderId): JsonResponse
    {
        return $this->storeInterface->getOneOrderData($id, $orderId);
    }

    public function getAllTypes(Request $request): JsonResponse
    {
        return $this->storeInterface->getAllTypesData($request);
    }

    public function getOneType(int $id): JsonResponse
    {
        return $this->storeInterface->getOneTypeData($id);
    }

    public function storeOneType(Request $request): JsonResponse
    {
        return $this->storeInterface->storeOneTypeData($request);
    }

    public function updateOneType(Request $request, int $id): JsonResponse
    {
        return $this->storeInterface->updateOneTypeData($request, $id);
    }

    public function removeOneType(int $id): JsonResponse
    {
        return $this->storeInterface->removeOneTypeData($id);
    }

    public function getAllCabangs(Request $request): JsonResponse
    {
        return $this->storeInterface->getAllCabangsData($request);
    }

    public function getOneCabang(int $id): JsonResponse
    {
        return $this->storeInterface->getOneCabangData($id);
    }

    public function storeOneCabang(Request $request): JsonResponse
    {
        return $this->storeInterface->storeOneCabangData($request);
    }

    public function updateOneCabang(Request $request, int $id): JsonResponse
    {
        return $this->storeInterface->updateOneCabangData($request, $id);
    }

    public function removeOneCabang(int $id): JsonResponse
    {
        return $this->storeInterface->removeOneCabangData($id);
    }
}
