<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Repositories\StoreInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class StoreInfoDistriController extends Controller
{
    protected StoreInterface $storeInterface;

    public function __construct(StoreInterface $storeInterface)
    {
        $this->storeInterface = $storeInterface;
    }

    public function getAllData(): JsonResponse
    {
        return $this->storeInterface->getAll();
    }

    public function getAllDataByQuery(Request $request): JsonResponse
    {
        return $this->storeInterface->getAllByQuery($request);
    }

    public function getAllDataByTypeFilter(Request $request): JsonResponse
    {
        return $this->storeInterface->getAllByTypeFilter($request);
    }

    public function getOneData(int $id): JsonResponse
    {
        return $this->storeInterface->getOne($id);
    }
}
