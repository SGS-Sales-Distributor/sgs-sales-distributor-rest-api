<?php

namespace App\Repositories\BasicAuthentication;

use App\Handlers\RandomDigitNumber;
use App\Models\User;
use App\Repositories\Repository;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;

class BasicAuthRepository extends Repository implements BasicAuthInterface
{
    public function login(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'email' => ['required', 'lowercase', 'string', 'email', 'max:255'],
                'password' => ['required', 'string'],
            ],
            [
                'required' => ':attribute is required!',
                'unique' => ':attribute is unique field!',
                'min' => ':attribute should be :min in characters',
                'max' => ':attribute could not more than :max characters',
                'confirmed' => ':attribute confirmation does not match!',  
            ]);
    
            if ($validator->fails()) {
                return $this->clientErrorResponse(
                    statusCode: 422,
                    success: false,
                    msg: $validator->errors()->first(),
                );
            }
    
            $credentials = $request->only('email', 'password');
    
            if (!Auth::attempt($credentials)) {
                return $this->clientErrorResponse(
                    statusCode: 401,
                    success: false,
                    msg: "Failed to authorize.",
                );
            }
    
            $salesman = User::with('type')
            ->where('email', $request->email)
            ->firstOrFail();
    
            if (!$salesman) {
                return $this->clientErrorResponse(
                    statusCode: 404, 
                    success: false, 
                    msg: "User not found.",
                );
            }
    
            if (!Hash::check($request->input('password'), $salesman->password)) {
                return $this->serverErrorResponse(
                    statusCode: 500, 
                    success: false, 
                    msg: "Password doesn't match.",
                );
            }
            
            return $this->successResponse(
                statusCode: 200,
                success: true,
                msg: "Successfully logged in as {$salesman->email}",
                resource: $salesman,
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


    public function logout(Request $request): JsonResponse
    {
        try {
            Auth::logout();

            return $this->successResponse(
                statusCode: 200,
                success: true,
                msg: "Successfully logout",
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

    public function resetPassword(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'email' => ['required', 'string', 'max:255', 'email'],
            'current_password' => ['required', 'string'],
            'password' => ['required', 'max:100', 'confirmed', Password::default()],
        ]);

        if ($validator->fails()) {
            return $this->clientErrorResponse(
                statusCode: 422,
                success: false,
                msg: $validator->errors()->first(),
            );
        }

        $salesman = User::where('email', $request->email)->firstOrFail();

        if (!Hash::check(request('current_password'), $salesman->password)) {
            return $this->serverErrorResponse(
                statusCode: 500, 
                success: false, 
                msg: "Password doesn't match.",
            );
        } else {
            try {
                DB::beginTransaction();
    
                $salesman->update([
                    'password' => $request->password,
                    'updated_at' => Carbon::now(env('APP_TIMEZONE'))->format('Y-m-d H:i:s'),
                ]);
    
                DB::commit();

                return $this->successResponse(
                    statusCode: 200,
                    success: true,
                    msg: "Successfully update salesman {$request->email} password data.",
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
    }
}