<?php

namespace App\Repositories;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

interface StoreInterface
{
    public function getAll(): JsonResponse;

    public function getAllOwners(): JsonResponse;

    public function getAllVisits(int $id): JsonResponse;

    public function getAllByQuery(Request $request): JsonResponse;
    
    public function getAllByOrderDateFilter(Request $request): JsonResponse;

    public function getOne(int $id): JsonResponse;

    public function getOneOwner(int $ownerId): JsonResponse;
}