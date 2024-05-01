<?php

namespace App\Http\Middleware;

use App\Handlers\JwtAuthToken;
use App\Traits\ApiResponse;
use Closure;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class JwtAuthMiddleware
{
    use ApiResponse;

    protected JwtAuthToken $jwtAuthToken;

    public function __construct(JwtAuthToken $jwtAuthToken)
    {
        $this->jwtAuthToken = $jwtAuthToken;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->bearerToken();

        if (!$token) {
            return $this->errorResponse(
                statusCode: 401,
                success: false,
                msg: "Uauthorized",
            );
        }

        $decodedToken = JWT::decode($token, new Key(env('JWT_SECRET_KEY'), 'HS512'));

        $request->merge(['decoded_token'=> (array) $decodedToken]);

        return $next($request);
    }
}
