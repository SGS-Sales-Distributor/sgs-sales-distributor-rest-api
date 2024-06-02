<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class OrderCustomerController extends Controller
{
    public function getAll(Request $request): JsonResponse
    {
        return $this->orderCustomerInterface->getAllData($request);
    }

    public function getOne(int $id): JsonResponse
    {
        return $this->orderCustomerInterface->getOneData($id);
    }

    public function getAllDetails(Request $request, int $id): JsonResponse
    {
        return $this->orderCustomerInterface->getAllDetailsData($request, $id);
    }

    public function getOneDetail(int $id, int $detailId): JsonResponse
    {
        return $this->orderCustomerInterface->getOneDetailData($id, $detailId);
    }

    public function storeOne(Request $request): JsonResponse
    {
        return $this->orderCustomerInterface->storeOneData($request);
    }

    public function updateOne(Request $request, int $id): JsonResponse
    {
        return $this->orderCustomerInterface->updateOneData($request, $id);
    }

    public function removeOne(int $id): JsonResponse
    {
        return $this->orderCustomerInterface->removeOneData($id);
    }
}