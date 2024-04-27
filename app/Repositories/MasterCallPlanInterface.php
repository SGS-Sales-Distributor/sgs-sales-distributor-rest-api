<?php

namespace App\Repositories;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

interface MasterCallPlanInterface
{
    public function getAll(): JsonResponse;

    public function getAllByQuery(Request $request): JsonResponse;

    public function getAllByDateFilter(Request $request): JsonResponse;

    public function getOne(int $id): JsonResponse;

    public function storeOne(Request $request): JsonResponse;

    public function updateOne(Request $request, int $id): JsonResponse;

    public function removeOne(int $id): JsonResponse;
}