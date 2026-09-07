<?php

namespace App\Repositories;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

interface UserCabangInterface
{
    public function getByUser(Request $request): JsonResponse;
    public function assignCabang(Request $request): JsonResponse;
    public function removeCabang(int $id): JsonResponse;
}
