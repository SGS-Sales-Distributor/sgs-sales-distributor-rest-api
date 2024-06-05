<?php

namespace App\Repositories;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

interface ProgramInterface
{
    public function getAllData(Request $request): JsonResponse;

    public function getDataByDateRangeFilter(Request $request): JsonResponse;
    
    public function getOneData(int $id): JsonResponse;

    public function getProgramTypeData(int $id): JsonResponse;

    public function getProgramDetailsData(int $id): JsonResponse;

    public function getOneProgramDetailData(int $id, int $detailId): JsonResponse;

    public function storeOneData(Request $request): JsonResponse;

    public function updateOneData(Request $request, int $id): JsonResponse;

    public function removeOneData(int $id): JsonResponse;
}