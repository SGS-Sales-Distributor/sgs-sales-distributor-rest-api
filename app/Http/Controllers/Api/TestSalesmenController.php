<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\StoreCabang;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TestSalesmenController extends Controller
{
    public function getAll(Request $request): JsonResponse
    {
        return $this->testSalesmanInterface->getAllData($request);
    }

    public function getOne(string $userNumber): JsonResponse
    {
        return $this->testSalesmanInterface->getOneData($userNumber);
    }

    public function storeOne(Request $request): JsonResponse
    {
        return $this->testSalesmanInterface->storeOneData($request);
    }

    public function checkInVisit(Request $request, string $userNumber): JsonResponse
    {
        return $this->testSalesmanInterface->checkInVisit($request, $userNumber);
    }

    public function checkOutVisit(Request $request, string $userNumber, int $visitId): JsonResponse
    {
        return $this->testSalesmanInterface->checkOutVisit($request, $userNumber, $visitId);
    }

    public function updateOne(Request $request, string $userNumber): JsonResponse
    {
        return $this->testSalesmanInterface->updateOneData($request, $userNumber);
    }

    public function updateProfile(Request $request, string $userNumber): JsonResponse
    {
        return $this->testSalesmanInterface->updateProfileData($request, $userNumber);
    }

    public function changePassword(Request $request, string $userNumber): JsonResponse
    {
        return $this->testSalesmanInterface->changePasswordData($request, $userNumber);
    }

    public function removeOne(string $userNumber): JsonResponse
    {
        return $this->testSalesmanInterface->removeOneData($userNumber);
    }

    public function getVisits(string $userNumber): JsonResponse
    {
        return $this->testSalesmanInterface->getVisitsData($userNumber);
    }

    public function countVisits(string $userNumber): JsonResponse
    {
        return $this->testSalesmanInterface->countVisitsData($userNumber);
    }

    public function getOneVisit(string $userNumber, int $visitId): JsonResponse
    {
        return $this->testSalesmanInterface->getOneVisitData($userNumber, $visitId);
    }

    public function getCallPlans(Request $request, string $userNumber): JsonResponse
    {
        return $this->testSalesmanInterface->getCallPlansData($request, $userNumber);
    }

    public function countVisitBasedOnCallPlans(string $userNumber): JsonResponse
    {
        return $this->testSalesmanInterface->countVisitBasedOnCallPlansData($userNumber);
    }

    public function getOneCallPlan(string $userNumber, int $callPlanId): JsonResponse
    {
        return $this->testSalesmanInterface->getOneCallPlanData($userNumber, $callPlanId);
    }

    public function getCboStoreCabang()
	{
		$type = (new StoreCabang())->getCboStoreCabang();
		return response()->json(
			['data' => $type],
			200
		);
	}
}
