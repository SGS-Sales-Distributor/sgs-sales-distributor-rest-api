<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Repositories\MasterCallPlanInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MasterCallPlanController extends Controller
{
    protected MasterCallPlanInterface $masterCallPlanInterface;

    public function __construct(MasterCallPlanInterface $masterCallPlanInterface)
    {
        $this->masterCallPlanInterface = $masterCallPlanInterface;
    }

    public function getAllData(): JsonResponse
    {
        return $this->masterCallPlanInterface->getAll();
    }

    public function getAllDataByQuery(Request $request): JsonResponse
    {
        return $this->masterCallPlanInterface->getAllByQuery($request);
    }

    public function getAllDataByDateFilter(Request $request): JsonResponse
    {
        return $this->masterCallPlanInterface->getAllByDateFilter($request);
    }

    public function getOneData(int $id): JsonResponse
    {
        return $this->masterCallPlanInterface->getOne($id);
    }
}
