<?php

namespace App\Repositories;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

interface MasterTypeProgramInterface
{
    public function getAll(): JsonResponse;

    public function getAllByQuery(Request $request): JsonResponse;

    public function getAllByPeriodeFilter(Request $request): JsonResponse;

    public function getOne(int $id): JsonResponse;
}