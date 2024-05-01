<?php

namespace App\Http\Controllers\Api\JwtAuthentication;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class JwtAuthController extends Controller
{
    public function login(Request $request): JsonResponse
    {
        return $this->jwtAuthInterface->login($request);
    }

    public function checkSelf(Request $request): JsonResponse
    {
        return $this->jwtAuthInterface->checkSelf($request);
    }

    public function refreshToken(Request $request): JsonResponse
    {
        return $this->jwtAuthInterface->refreshToken($request);
    }
}
