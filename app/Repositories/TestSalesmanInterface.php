<?php

namespace App\Repositories;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

interface TestSalesmanInterface
{
    public function getAllData(Request $request): JsonResponse;

    public function getOneData(string $userNumber): JsonResponse;

    public function storeOneData(Request $request): JsonResponse;

    public function checkInVisit(Request $request, string $userNumber): JsonResponse;

    public function checkOutVisit(Request $request, string $userNumber, int $visitId): JsonResponse;

    public function updateOneData(Request $request, string $userNumber): JsonResponse;

    public function updateProfileData(Request $request, string $userNumber): JsonResponse;

    public function changePasswordData(Request $request, string $userNumber): JsonResponse;

    public function removeOneData(string $userNumber): JsonResponse;

    public function getVisitsData(string $userNumber): JsonResponse;

    public function countVisitsData(string $userNumber): JsonResponse;
    
    public function getOneVisitData(string $userNumber, int $visitId): JsonResponse;

    public function getCallPlansData(Request $request, string $userNumber): JsonResponse;

    public function countVisitBasedOnCallPlansData(string $userNumber): JsonResponse;
    
    public function getOneCallPlanData(string $userNumber, int $callPlanId): JsonResponse;
}