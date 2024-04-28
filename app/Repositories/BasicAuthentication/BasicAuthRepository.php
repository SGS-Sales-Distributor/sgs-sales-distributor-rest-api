<?php

namespace App\Repositories\BasicAuthentication;

use App\Handlers\RandomDigitNumber;
use App\Models\User;
use App\Repositories\Repository;
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
    
            $salesman = User::where(
                'email', 
                $request->email
            )->firstOrFail();
    
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
    
            $request->session()->regenerate();
            
            return $this->successResponse(
                statusCode: 200,
                success: true,
                msg: "Successfully logged in as {$salesman->email}",
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



    public function register(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'nik' => ['required', 'string', 'unique:user_info,nik', 'max:20'],
            'fullname' => ['required', 'string', 'max:200'],
            'phone_number' => ['required', 'string', 'unique:user_info,phone'],
            'email '=> ['nullable', 'lowercase', 'string', 'email', 'max:255', 'unique:user_info,email'],
            'username' => ['required', 'string', 'max:50', 'unique:user_info,username'],
            'password' => ['required', 'max:50', 'confirmed', Password::default()],
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

        try {
            DB::beginTransaction();

            $newSalesman = User::create([
                'number' => $this->randomDigitNumber->generateRandomNumber(),
                'nik' => $request->nik,
                'fullname' => $request->fullname,
                'phone' => $request->phone_number,
                'email' => $request->email,
                'username' => $request->username,
                'password' => $request->password,
            ]);

            DB::commit();

            $checkSalesman = User::where('id', $newSalesman->id)
            ->firstOrFail();

            return $this->successResponse(
                statusCode: 201, 
                success: true, 
                msg: "Successfully create new salesman data", 
                resource: $checkSalesman
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

            $request->session()->invalidate();

            $request->session()->regenerateToken();

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
}