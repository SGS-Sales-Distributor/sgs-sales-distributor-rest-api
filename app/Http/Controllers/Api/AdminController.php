<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function getAll(Request $request): JsonResponse
    {
        return $this->adminInterface->getAllData($request);
    }

    public function getOne(string $name): JsonResponse
    {
        return $this->adminInterface->getOneData($name);
    }

    public function storeOne(Request $request): JsonResponse
    {
        return $this->adminInterface->storeOneData($request);
    }

    public function updateOne(Request $request, string $name): JsonResponse
    {
        return $this->adminInterface->updateOneData($request, $name);
    }

    public function updateProfile(Request $request, string $name): JsonResponse
    {
        return $this->adminInterface->updateProfileData($request, $name);
    }

    public function removeOne(string $name): JsonResponse
    {
        return $this->adminInterface->removeOneData($name);
    }
}
