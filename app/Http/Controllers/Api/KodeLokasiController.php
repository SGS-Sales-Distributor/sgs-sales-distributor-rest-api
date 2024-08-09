<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

class KodeLokasiController extends Controller
{
    public function getAll(Request $request): JsonResponse
    {
        return $this->kodeLokasiInterface->getAllData($request);
    }

    public function getOne(string $id): JsonResponse
    {
        return $this->kodeLokasiInterface->getOneData($id);
    }
}
