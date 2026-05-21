<?php

namespace App\Repositories;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

interface OutletRequestInterface
{
    public function submitRequest(Request $request): JsonResponse;
    public function getRequestsByAS(Request $request, int $userId): JsonResponse;
    public function approveRequest(Request $request, int $id): JsonResponse;
    public function rejectRequest(Request $request, int $id): JsonResponse;
}