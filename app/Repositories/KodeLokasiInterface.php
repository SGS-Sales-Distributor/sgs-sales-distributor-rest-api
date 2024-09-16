<?php

namespace App\Repositories;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

interface KodeLokasiInterface
{
    public function getAllData(Request $request): JsonResponse;

    public function getOneData(string $id): JsonResponse;
}