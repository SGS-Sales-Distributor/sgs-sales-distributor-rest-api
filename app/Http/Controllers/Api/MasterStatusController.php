<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MasterStatusController extends Controller
{
    public function getAllData(): JsonResponse
    {
        return $this->masterStatusInterface->getAll();
    }

    public function getAllDataByQuery(Request $request): JsonResponse
    {
        return $this->masterStatusInterface->getAllByQuery($request);
    }

    public function getAllDataByOrderDateFilter(Request $request): JsonResponse
    {
        return $this->masterStatusInterface->getAllByOrderDateFilter($request);
    }

    public function getOneData(int $id): JsonResponse
    {
        return $this->masterStatusInterface->getOne($id);
    }
}
