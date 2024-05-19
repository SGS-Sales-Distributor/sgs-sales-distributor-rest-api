<?php

namespace App\Repositories;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

interface MasterCallPlanInterface
{
    public function getAllData(Request $request): JsonResponse;

    public function getAllDataByDateFilter(Request $request): JsonResponse;

    public function getOneData(int $id): JsonResponse;

    public function storeOneData(Request $request): JsonResponse;

    public function updateOneData(Request $request, int $id): JsonResponse;

    public function removeOneData(int $id): JsonResponse;
}