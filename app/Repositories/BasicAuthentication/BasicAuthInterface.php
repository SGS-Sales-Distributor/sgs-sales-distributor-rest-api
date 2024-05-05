<?php

namespace App\Repositories\BasicAuthentication;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

interface BasicAuthInterface
{   
    public function login(Request $request): JsonResponse;

    public function logout(Request $request): JsonResponse;

    public function resetPassword(Request $request): JsonResponse;
}
