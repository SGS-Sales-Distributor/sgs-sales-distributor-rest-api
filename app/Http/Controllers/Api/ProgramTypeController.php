<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProgramTypeController extends Controller
{
    public function getAllData(Request $request): JsonResponse
    {
        return $this->programTypeInterface->getAll($request);
    }

    public function getOneData(int $id): JsonResponse
    {
        return $this->programTypeInterface->getOne($id);
    }

    public function storeNewProgramType(Request $request): JsonResponse
    {
        return $this->programTypeInterface->storeOne($request);
    }

    public function updateRecentProgramType(Request $request, int $id): JsonResponse
    {
        return $this->programTypeInterface->updateOne($request, $id);
    }

    public function removeRecentProgramType(int $id): JsonResponse
    {
        return $this->programTypeInterface->removeOne($id);
    }
}
