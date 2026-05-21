<?php

namespace App\Repositories;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

interface MasterCustomerInterface
{
    public function getAllData(Request $request): JsonResponse;
    public function getOneData(string $id): JsonResponse;
    public function createData(Request $request): JsonResponse;
    public function updateData(Request $request, string $id): JsonResponse;
    public function deleteData(string $id): JsonResponse;
}