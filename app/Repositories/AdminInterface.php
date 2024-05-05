<?php

namespace App\Repositories;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

interface AdminInterface
{
    public function getAllData(Request $request): JsonResponse;

    public function getOneData(string $name): JsonResponse;

    public function storeOneData(Request $request): JsonResponse;

    public function updateOneData(Request $request, string $name): JsonResponse;
    
    public function updateProfileData(Request $request, string $name): JsonResponse;

    public function removeOneData(string $name): JsonResponse;
}