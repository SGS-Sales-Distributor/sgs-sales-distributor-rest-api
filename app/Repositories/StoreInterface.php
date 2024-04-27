<?php

namespace App\Repositories;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

interface StoreInterface
{
    public function getAll(): JsonResponse;

    public function getAllByQuery(Request $request): JsonResponse;
    
    public function getAllByTypeFilter(Request $request): JsonResponse;

    public function getOne(int $id): JsonResponse;
}