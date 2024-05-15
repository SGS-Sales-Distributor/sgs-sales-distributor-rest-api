<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MasterCallPlanController extends Controller
{
    public function getAll(Request $request): JsonResponse
    {
        return $this->masterCallPlanInterface->getAllData($request);
    }

    public function getAllByDateFilter(Request $request): JsonResponse
    {
        return $this->masterCallPlanInterface->getAllDataByDateFilter($request);
    }

    public function getOne(int $id): JsonResponse
    {
        return $this->masterCallPlanInterface->getOneData($id);
    }

    public function storeOne(Request $request): JsonResponse
    {
        return $this->masterCallPlanInterface->storeOneData($request);
    }

    public function updateOne(Request $request, int $id): JsonResponse
    {
        return $this->masterCallPlanInterface->updateOneData($request, $id);
    }

    public function removeOne(int $id): JsonResponse
    {
        return $this->masterCallPlanInterface->removeOneData($id);
    }
}
