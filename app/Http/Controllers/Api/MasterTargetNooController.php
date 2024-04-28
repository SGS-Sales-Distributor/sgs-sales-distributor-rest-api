<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MasterTargetNooController extends Controller
{
    public function getAllData(): JsonResponse
    {
        return $this->masterTargetNooInterface->getAll();
    }

    public function getAllDataByQuery(Request $request): JsonResponse
    {
        return $this->masterTargetNooInterface->getAllByQuery($request);
    }

    public function getAllDataByYearFilter(Request $request): JsonResponse
    {
        return $this->masterTargetNooInterface->getAllByYearFilter($request);
    }

    public function getOneData(int $id): JsonResponse
    {
        return $this->masterTargetNooInterface->getOne($id);
    }
}
