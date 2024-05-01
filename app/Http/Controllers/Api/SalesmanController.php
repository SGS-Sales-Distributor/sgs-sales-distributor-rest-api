<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SalesmanController extends Controller
{
    public function getAllData(): JsonResponse
    {
        return $this->salesmanInterface->getAll();
    }

    public function getAllVisitsData(int $userId): JsonResponse
    {
        return $this->salesmanInterface->getAllVisits($userId);
    }

    public function getOneVisitData(int $userId, int $visitId): JsonResponse
    {
        return $this->salesmanInterface->getOneVisit($userId, $visitId);
    }

    public function getOneData(int $userId): JsonResponse
    {
        return $this->salesmanInterface->getOne($userId);
    }

    public function storeOneData(Request $request): JsonResponse
    {
        return $this->salesmanInterface->storeOne($request);
    }

    public function checkInVisit(Request $request, string $userNumber): JsonResponse
    {
        return $this->salesmanInterface->checkInVisit($request, $userNumber);
    }

    public function checkOutVisit(Request $request, string $userNumber, int $visitId): JsonResponse
    {
        return $this->salesmanInterface->checkOutVisit($request, $userNumber, $visitId);
    }
}
