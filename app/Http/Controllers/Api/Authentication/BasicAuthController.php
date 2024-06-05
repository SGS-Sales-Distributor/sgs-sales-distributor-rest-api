<?php

namespace App\Http\Controllers\Api\Authentication;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BasicAuthController extends Controller
{
    /**
     * Login as existing salesman account.
     */
    public function login(Request $request): JsonResponse
    {
        return $this->basicAuthInterface->login($request);
    }

    /**
     * Logout from salesman account.
     */
    public function logout(Request $request): JsonResponse
    {
        return $this->basicAuthInterface->logout($request);
    }

    public function resetPassword(Request $request): JsonResponse
    {
        return $this->basicAuthInterface->resetPassword($request);
    }
}
