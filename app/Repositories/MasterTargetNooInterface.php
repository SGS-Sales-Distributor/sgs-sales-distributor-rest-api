<?php

namespace App\Repositories;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

interface MasterTargetNooInterface
{
    public function getAll(): JsonResponse;

    public function getAllByQuery(Request $request): JsonResponse;

    public function getAllByDateFilter(Request $request): JsonResponse;

    public function getOne(int $id): JsonResponse;
}