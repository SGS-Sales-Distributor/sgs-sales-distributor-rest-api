<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProgramTypeController extends Controller
{
    public function getAll(Request $request): JsonResponse
    {
        return $this->programTypeInterface->getAllData($request);
    }

    public function getOne(int $id): JsonResponse
    {
        return $this->programTypeInterface->getOneData($id);
    }

    public function storeOne(Request $request): JsonResponse
    {
        return $this->programTypeInterface->storeOneData($request);
    }

    public function updateOne(Request $request, int $id): JsonResponse
    {
        return $this->programTypeInterface->updateOneData($request, $id);
    }

    public function removeOne(int $id): JsonResponse
    {
        return $this->programTypeInterface->removeOneData($id);
    }
}
