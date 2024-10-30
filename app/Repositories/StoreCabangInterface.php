<?php

namespace App\Repositories;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

interface StoreCabangInterface
{
    public function getAllData(Request $request): JsonResponse;
}
