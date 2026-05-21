<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Repositories\CabangRequestInterface;
use Illuminate\Http\Request;

class CabangRequestController extends Controller
{
    public function __construct(protected CabangRequestInterface $cabangRequestRepository) {}

    // Sales: riwayat request milik sendiri
    public function myRequests(Request $request)
    {
        return $this->cabangRequestRepository->getMyRequests($request);
    }

    // Sales: cabang yang sudah dimiliki
    public function myCabangs(Request $request)
    {
        return $this->cabangRequestRepository->getMyCabangs($request);
    }

    // AS: semua request dari MD bawahan
    public function index(Request $request)
    {
        return $this->cabangRequestRepository->getAllRequests($request);
    }

    // Sales: kirim request cabang baru
    public function store(Request $request)
    {
        return $this->cabangRequestRepository->storeRequest($request);
    }

    // AS: approve
    public function approve(Request $request, int $id)
    {
        return $this->cabangRequestRepository->approveRequest($request, $id);
    }

    // AS: reject
    public function reject(Request $request, int $id)
    {
        return $this->cabangRequestRepository->rejectRequest($request, $id);
    }
}