<?php

namespace App\Repositories\BasicAuthentication;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

interface BasicAuthInterface
{   
    public function login(Request $request): JsonResponse;

    public function register(Request $request): JsonResponse;

    public function logout(Request $request): JsonResponse;
}
