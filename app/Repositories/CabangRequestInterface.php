<?php

namespace App\Repositories;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

interface CabangRequestInterface
{
    public function getMyCabangs(Request $request): JsonResponse;
    public function getMyRequests(Request $request): JsonResponse;
    public function getAllRequests(Request $request): JsonResponse;
    public function storeRequest(Request $request): JsonResponse;
    public function approveRequest(Request $request, int $id): JsonResponse;
    public function rejectRequest(Request $request, int $id): JsonResponse;
}