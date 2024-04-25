<?php

namespace App\Http\Controllers\Api\Authentication;

use App\Http\Controllers\Controller;
use App\Repositories\BasicAuthentication\BasicAuthInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BasicAuthController extends Controller
{
    protected BasicAuthInterface $basicAuthInterface;

    public function __construct(BasicAuthInterface $basicAuthInterface)
    {
        $this->basicAuthInterface = $basicAuthInterface;
    }

    /**
     * Login as existing salesman account.
     */
    public function login(Request $request): JsonResponse
    {
        return $this->basicAuthInterface->login($request);
    }

    /**
     * Register new salesman account.
     */
    public function register(Request $request): JsonResponse
    {
        return $this->basicAuthInterface->register($request);
    }

    /**
     * Logout from salesman account.
     */
    public function logout(Request $request): JsonResponse
    {
        return $this->basicAuthInterface->logout($request);
    }
}
