<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Repositories\OutletRequestRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class OutletRequestController extends Controller
{
    public function __construct(
        protected OutletRequestRepository $outletRequestRepository
    ) {}

    public function submitRequest(Request $request): JsonResponse
    {
        return $this->outletRequestRepository->submitRequest($request);
    }

    public function getRequestsByAS(Request $request, int $userId): JsonResponse
    {
        return $this->outletRequestRepository->getRequestsByAS($request, $userId);
    }

    public function approveRequest(Request $request, int $id): JsonResponse
    {
        return $this->outletRequestRepository->approveRequest($request, $id);
    }

    public function rejectRequest(Request $request, int $id): JsonResponse
    {
        return $this->outletRequestRepository->rejectRequest($request, $id);
    }
}