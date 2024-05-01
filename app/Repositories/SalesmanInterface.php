<?php

namespace App\Repositories;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

interface SalesmanInterface
{
    public function getAll(): JsonResponse;

    public function getOne(int $userId): JsonResponse;

    public function getAllVisits(int $userId): JsonResponse;

    public function getOneVisit(int $userId, int $visitId): JsonResponse;

    public function getAllCallPlans(int $userId): JsonResponse;

    public function getOneCallPlan(int $userId, int $masterCallPlanId): JsonResponse;

    public function getAllByQuery(Request $request): JsonResponse;

    // public function storeOneUserType(Request $request): JsonResponse;

    // public function storeOneUserStatus(Request $request): JsonResponse;

    public function storeOne(Request $request): JsonResponse;

    public function checkInVisit(Request $request, string $userNumber): JsonResponse;

    public function checkOutVisit(Request $request, string $userNumber, int $visitId): JsonResponse;
}