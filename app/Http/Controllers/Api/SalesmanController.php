<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SalesmanController extends Controller
{
    public function getAll(Request $request): JsonResponse
    {
        return $this->salesmanInterface->getAllData($request);
    }

    public function getOne(string $userNumber): JsonResponse
    {
        return $this->salesmanInterface->getOneData($userNumber);
    }

    public function storeOne(Request $request): JsonResponse
    {
        return $this->salesmanInterface->storeOneData($request);
    }

    public function checkInVisit(Request $request, string $userNumber): JsonResponse
    {
        return $this->salesmanInterface->checkInVisit($request, $userNumber);
    }

    public function checkOutVisit(Request $request, string $userNumber, int $visitId): JsonResponse
    {
        return $this->salesmanInterface->checkOutVisit($request, $userNumber, $visitId);
    }

    public function updateOne(Request $request, string $userNumber): JsonResponse
    {
        return $this->salesmanInterface->updateOneData($request, $userNumber);
    }

    public function updateProfile(Request $request, string $userNumber): JsonResponse
    {
        return $this->salesmanInterface->updateProfileData($request, $userNumber);
    }

    public function removeOne(string $userNumber): JsonResponse
    {
        return $this->salesmanInterface->removeOneData($userNumber);
    }

    public function getVisits(string $userNumber): JsonResponse
    {
        return $this->salesmanInterface->getVisitsData($userNumber);
    }

    public function getOneVisit(string $userNumber, int $visitId): JsonResponse
    {
        return $this->salesmanInterface->getOneVisitData($userNumber, $visitId);
    }

    public function getCallPlans(string $userNumber): JsonResponse
    {
        return $this->salesmanInterface->getCallPlansData($userNumber);
    }

    public function getOneCallPlan(string $userNumber, int $callPlanId): JsonResponse
    {
        return $this->salesmanInterface->getOneCallPlanData($userNumber, $callPlanId);
    }
}
