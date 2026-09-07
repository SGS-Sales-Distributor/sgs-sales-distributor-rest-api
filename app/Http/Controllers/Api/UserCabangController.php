<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Repositories\UserCabangInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserCabangController extends Controller
{
    public function __construct(protected UserCabangInterface $userCabangInterface) {}

    public function index(Request $request): JsonResponse
    {
        return $this->userCabangInterface->getByUser($request);
    }

    public function store(Request $request): JsonResponse
    {
        return $this->userCabangInterface->assignCabang($request);
    }

    public function destroy(int $id): JsonResponse
    {
        return $this->userCabangInterface->removeCabang($id);
    }
}