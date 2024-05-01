<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class StoreInfoDistriController extends Controller
{
    public function getAllData(): JsonResponse
    {
        return $this->storeInterface->getAll();
    }

    public function getOneData(int $id): JsonResponse
    {
        return $this->storeInterface->getOne($id);
    }

    public function getAllVisitsData(int $id): JsonResponse
    {
        return $this->storeInterface->getAllVisits($id);
    }

    public function getAllDataByQuery(Request $request): JsonResponse
    {
        return $this->storeInterface->getAllByQuery($request);
    }

    public function getAllDataByOrderDateFilter(Request $request): JsonResponse
    {
        return $this->storeInterface->getAllByOrderDateFilter($request);
    }
}
