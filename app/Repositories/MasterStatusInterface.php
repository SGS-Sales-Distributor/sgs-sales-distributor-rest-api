<?php

namespace App\Repositories;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

interface MasterStatusInterface
{
    public function getAll(): JsonResponse;

    public function getAllByQuery(Request $request): JsonResponse;

    public function getAllByOrderDateFilter(Request $request): JsonResponse;

    public function getOne(int $id): JsonResponse;
}