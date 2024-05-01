<?php

namespace App\Repositories\JwtAuthentication;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

interface JwtAuthInterface
{
    public function login(Request $request): JsonResponse;

    public function checkSelf(Request $request): JsonResponse;

    public function refreshToken(Request $request): JsonResponse;
}