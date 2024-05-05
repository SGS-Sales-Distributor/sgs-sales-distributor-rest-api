<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProgramController extends Controller
{
    public function getAll(Request $request): JsonResponse
    {
        return $this->programInterface->getAllData($request);
    }

    public function getAllDataByPeriodeFilter(Request $request): JsonResponse
    {
        return $this->programInterface->getAllByPeriodeFilter($request);
    }

    public function getOne(int $id): JsonResponse
    {
        return $this->programInterface->getOneData($id);
    }

    public function getProgramType(int $id): JsonResponse
    {
        return $this->programInterface->getProgramTypeData($id);
    }

    public function getProgramDetails(int $id): JsonResponse
    {
        return $this->programInterface->getProgramDetailsData($id);
    }

    public function getOneProgramDetail(int $id, int $detailId): JsonResponse
    {
        return $this->programInterface->getOneProgramDetailData($id, $detailId);
    }

    public function storeOne(Request $request): JsonResponse
    {
        return $this->programInterface->storeOneData($request);
    }

    public function updateOne(Request $request, int $id): JsonResponse
    {
        return $this->programInterface->updateOneData($request, $id);
    }

    public function removeOne(int $id): JsonResponse
    {
        return $this->programInterface->removeOneData($id);
    }
}
