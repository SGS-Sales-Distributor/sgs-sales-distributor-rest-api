<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MasterTypeProgramController extends Controller
{
    public function getAllData(): JsonResponse
    {
        return $this->masterTypeProgramInterface->getAll();
    }

    public function getAllDataByQuery(Request $request): JsonResponse
    {
        return $this->masterTypeProgramInterface->getAllByQuery($request);
    }

    public function getAllDataByPeriodeFilter(Request $request): JsonResponse
    {
        return $this->masterTypeProgramInterface->getAllByPeriodeFilter($request);
    }

    public function getOneData(int $id): JsonResponse
    {
        return $this->masterTypeProgramInterface->getOne($id);
    }
}
