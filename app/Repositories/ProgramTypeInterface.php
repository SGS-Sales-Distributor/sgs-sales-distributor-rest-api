<?php

namespace App\Repositories;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

interface ProgramTypeInterface
{
    public function getAll(Request $request): JsonResponse;

    public function getOne(int $id): JsonResponse;

    public function storeOne(Request $request): JsonResponse;

    public function updateOne(Request $request, int $id): JsonResponse;

    public function removeOne(int $id): JsonResponse;
}