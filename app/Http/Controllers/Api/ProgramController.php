<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProgramController extends Controller
{
    public function getAllData(): JsonResponse
    {
        return $this->programInterface->getAll();
    }

    public function getAllDataByQuery(Request $request): JsonResponse
    {
        return $this->programInterface->getAllByQuery($request);
    }

    public function getAllDataByPeriodeFilter(Request $request): JsonResponse
    {
        return $this->programInterface->getAllByPeriodeFilter($request);
    }

    public function getOneData(int $id): JsonResponse
    {
        return $this->programInterface->getOne($id);
    }
}
