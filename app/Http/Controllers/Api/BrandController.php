<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BrandController extends Controller
{
    public function getAll(Request $request): JsonResponse
    {
        return $this->brandInterface->getAllData($request);
    }

    public function getOne(string $id): JsonResponse
    {
        return $this->brandInterface->getOneData($id);
    }
}