<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Repositories\MasterTargetNooInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MasterTargetNooController extends Controller
{
    protected MasterTargetNooInterface $masterTargetNooInterface;

    public function __construct(MasterTargetNooInterface $masterTargetNooInterface)
    {
        $this->masterTargetNooInterface = $masterTargetNooInterface;
    }

    public function getAllData(): JsonResponse
    {
        return $this->masterTargetNooInterface->getAll();
    }

    public function getAllDataByQuery(Request $request): JsonResponse
    {
        return $this->masterTargetNooInterface->getAllByQuery($request);
    }

    public function getAllDataByDateFilter(Request $request): JsonResponse
    {
        return $this->masterTargetNooInterface->getAllByDateFilter($request);
    }

    public function getOneData(int $id): JsonResponse
    {
        return $this->masterTargetNooInterface->getOne($id);
    }
}
