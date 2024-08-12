<?php

namespace App\Repositories\JwtAuthentication;

use App\Models\User;
use App\Repositories\Repository;
use Carbon\Carbon;
use DateTimeImmutable;
use DateTimeZone;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;
use Exception;

class JwtAuthRepository extends Repository implements JwtAuthInterface
{
    public function login(Request $request): JsonResponse
    {
        # validate data.
        $validator = Validator::make(
            $request->all(),
            [
                'email' => ['required', 'lowercase', 'string', 'email'],
                'password' => ['required', 'string'],
            ],
            [
                'required' => ':attribute is required!',
                'unique' => ':attribute is unique field!',
                'min' => ':attribute should be :min in characters',
                'max' => ':attribute could not more than :max characters',
                'confirmed' => ':attribute confirmation does not match!',
            ]
        );

        if ($validator->fails()) {
            return $this->clientErrorResponse(
                statusCode: 422,
                success: false,
                msg: $validator->errors()->first(),
            );
        }


        # credential for auth attempt.
        $credentials = $request->only('email', 'password');
        $atuhAttm = Auth::attempt($credentials);
        if (!$atuhAttm) {
            return $this->clientErrorResponse(
                statusCode: 401,
                success: false,
                // msg: "Failed to authorized.",
                msg: "Email atau Password Salah!",
            );
        }

        # search user.
        $user = User::where('email', $request->email)
            ->firstOrFail();

        return $this->ErrorResponse(
            statusCode: 401,
            success: false,
            // msg: "Failed to authorized.",
            msg: "User Email Belum Terdaftar!",
        );


        # generate tokens.
        $jwt = $this->jwtAuthToken->generateToken($user);
        $refreshToken = $this->jwtAuthToken->generateRefreshToken($user);

        $body = [
            'tokens' => [
                'access_token' => $jwt,
                'refresh_token' => $refreshToken,
            ],
            'type' => 'bearer',
        ];

        return $this->successResponse(
            statusCode: 200,
            success: true,
            msg: "Successfully login as {$user->email}",
            resource: $body
        );
    }

    public function register(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            // 'number' => ['nullable', 'string', 'max:10'],
            // 'nik' => ['required', 'string', 'max:20'],
            'area' => ['nullable', 'integer'],
            'fullname' => ['required', 'string', 'max:200'],
            'phone' => ['required', 'string', 'max:20', 'unique:user_info,phone'],
            'email' => ['required', 'string', 'email', 'lowercase', 'max:255', 'unique:user_info,email'],
            'name' => ['nullable', 'string', 'max:50'],
            'password' => ['required', 'confirmed', Password::min(8)->mixedCase()],
            'type_id' => ['nullable', 'integer'],
            'status' => ['nullable', 'integer'],
        ]);

        if ($validator->fails()) {
            return $this->clientErrorResponse(
                statusCode: 422,
                success: false,
                msg: $validator->errors()->first(),
            );
        }

        try {
            DB::beginTransaction();

            $lastId = User::orderBy('user_id', 'desc')->first()->user_id;
            $setLastId = $lastId + 1;

            //generate NIK
            $month = date('m');
            $year = date('y');
            $generateNik = '06' . $request->kode_lokasi . $month . $year . sprintf('%05d', $setLastId);

            $user = User::create([
                // 'number' => $request->number,
                'number' => sprintf('%06d', $setLastId),
                'nik' => $generateNik,
                'fullname' => $request->fullname,
                'phone' => $request->phone,
                'email' => $request->email,
                'name' => $request->fullname,
                'password' => $request->password,
                'type_id' => 2,
                'status' => 2,
            ]);

            DB::commit();

            return $this->successResponse(
                statusCode: 201,
                success: true,
                msg: "Successfully register new salesman account",
                resource: $user
            );
        } catch (\Symfony\Component\HttpKernel\Exception\HttpException $e) {
            DB::rollBack();

            return $this->errorResponse(
                statusCode: $e->getStatusCode(),
                success: false,
                msg: $e->getMessage(),
            );
        } catch (\Error $e) {
            DB::rollBack();

            return $this->errorResponse(
                statusCode: 500,
                success: false,
                msg: $e->getMessage(),
            );
        } catch (\Exception $e) {
            DB::rollBack();

            return $this->errorResponse(
                statusCode: 500,
                success: false,
                msg: $e->getMessage(),
            );
        }
    }

    public function checkSelf(Request $request): JsonResponse
    {
        $jwt = $request->decoded_token;

        $decryptUser = $this->jwtAuthToken->decryptUserData($jwt['user']);

        # convert into datetime with tz from env.
        $currentTime = new DateTimeImmutable(timezone: new DateTimeZone(env('APP_TIMEZONE')));
        $tokenExpTime = Carbon::createFromTimestamp($jwt['exp'], env('APP_TIMEZONE'));

        # just return your shit.
        return $this->successResponse(
            statusCode: 200,
            success: true,
            msg: "Successfully fetch your data.",
            resource: [
                'data' => $decryptUser,
                'current_time' => $currentTime->getTimestamp(),
                'token_expiration_time' => $jwt['exp'],
                'current_datetime' => Carbon::now(timezone: env('APP_TIMEZONE'))->format('Y-m-d H:i:s'),
                'token_expiration_datetime' => $tokenExpTime->format('Y-m-d H:i:s'),
            ],
        );
    }

    public function refreshToken(Request $request): JsonResponse
    {

        $validator = Validator::make(
            $request->all(),
            [
                'refresh_token' => ['required', 'string'],
            ],
            [
                'required' => ':attribute is required!',
                'string' => ':attribute should be a string | token',
            ]
        );

        if ($validator->fails()) {
            return $this->clientErrorResponse(
                statusCode: 422,
                success: false,
                msg: $validator->errors()->first(),
            );
        }

        $decodeRefreshToken = JWT::decode($request->refresh_token, new Key(env('REFRESH_SECRET_KEY'), 'HS512'));

        $user = User::where('number', $decodeRefreshToken->sub)->firstOrFail();

        $newJwt = $this->jwtAuthToken->generateToken($user);

        return $this->successResponse(
            statusCode: 200,
            success: true,
            msg: "Successfully refresh token",
            resource: [
                'access_token' => $newJwt,
                'type' => 'bearer',
            ],
        );
    }
}