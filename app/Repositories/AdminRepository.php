<?php

namespace App\Repositories;

use App\Models\MasterUser;
use App\Models\MasterUserDetail;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;

class AdminRepository extends Repository implements AdminInterface
{
    public function getAllData(Request $request): JsonResponse
    {
        $searchByQuery = $request->query('q');

        $adminsCache = Cache::remember(
            "adminsCache", 
            $this::DEFAULT_CACHE_TTL, 
            function () use ($searchByQuery) 
        {
            return MasterUser::with('details')
            ->when($searchByQuery, function (Builder $query) use ($searchByQuery) {
                $query->where('user', 'LIKE', '%' . $searchByQuery . '%');
            })
            ->orderBy('user', 'asc')
            ->paginate($this::DEFAULT_PAGINATE); 
        });

        return $this->successResponse(
            statusCode: 200,
            success: true,
            msg: "Successfully fetch admins data.",
            resource: $adminsCache,
        );
    }

    public function getOneData(string $name): JsonResponse
    {
        $adminCache = Cache::remember("admins:{$name}", $this::DEFAULT_CACHE_TTL, function () use ($name) {
            return MasterUser::with('details')
            ->where('user', $name)
            ->firstOrFail();
        });

        return $this->successResponse(
            statusCode: 200,
            success: true,
            msg: "Successfully fetch admins {$name} data.",
            resource: $adminCache,
        );
    }

    public function storeOneData(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'user' => ['required', 'string', 'max:100'],
            'description' => ['nullable', 'string', 'max:255'],
            'password' => ['required', 'max:255', 'confirmed', Password::default()],
            'username' => ['required', 'max:50', 'string', 'unique:master_user,username'],
            'defaultpassword' => ['nullable', 'string', 'max:50'],
            'nik' => ['required', 'string', 'max:50', 'unique:master_user,nik'],
        ]);

        if ($validator->fails()) {
            return $this->clientErrorResponse(
                statusCode: 422,
                success: false,
                msg: $validator->errors()->first(),
                resource: $validator->errors()->all(),
            );
        }

        try {
            DB::beginTransaction();

            $admin = MasterUser::create([
                'user' => $request->user,
                'description' => $request->description,
                'password' => $request->password,
                'username' => $request->username,
                'defaultpassword' => $request->defaultpassword,
                'nik' => $request->nik
            ]);

            DB::commit();

            return $this->successResponse(
                statusCode: 201,
                success: true,
                msg: "Successfully create new admin data.",
                resource: $admin,
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

    public function updateOneData(Request $request, string $name): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'user' => ['required', 'string', 'max:100'],
            'description' => ['nullable', 'string', 'max:255'],
            'password' => ['required', 'max:255', 'confirmed', Password::default()],
            'username' => ['required', 'max:50', 'string', 'unique:master_user,username'],
            'defaultpassword' => ['nullable', 'string', 'max:50'],
            'nik' => ['required', 'string', 'max:50', 'unique:master_user,nik'],
        ]);

        if ($validator->fails()) {
            return $this->clientErrorResponse(
                statusCode: 422,
                success: false,
                msg: $validator->errors()->first(),
                resource: $validator->errors()->all(),
            );
        }

        $admin = MasterUser::where('user', $name)->firstOrFail();

        try {
            DB::beginTransaction();

            $admin->update([
                'user' => $request->user,
                'description' => $request->description,
                'password' => $request->password,
                'username' => $request->username,
                'defaultpassword' => $request->defaultpassword,
                'nik' => $request->nik,
            ]);

            DB::commit();

            return $this->successResponse(
                statusCode: 200,
                success: true,
                msg: "Successfully update recent admin {$name} data.",
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

    public function updateProfileData(Request $request, string $name): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'user' => ['required', 'string', 'max:100'],
            'description' => ['nullable', 'string', 'max:255'],
            'username' => ['required', 'max:50', 'string', 'unique:master_user,username'],
            'nik' => ['required', 'string', 'max:50', 'unique:master_user,nik'],
        ]);

        if ($validator->fails()) {
            return $this->clientErrorResponse(
                statusCode: 422,
                success: false,
                msg: $validator->errors()->first(),
                resource: $validator->errors()->all(),
            );
        }

        $admin = MasterUser::where('user', $name)->firstOrFail();

        try {
            DB::beginTransaction();

            $admin->update([
                'user' => $request->user,
                'description' => $request->description,
                'username' => $request->username,
                'nik' => $request->nik,
            ]);

            DB::commit();

            return $this->successResponse(
                statusCode: 200,
                success: true,
                msg: "Successfully update profile of recent admin {$name} data.",
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

    public function removeOneData(string $name): JsonResponse
    {
        $adminDetail = MasterUserDetail::where('user', $name)->firsOrFail();

        $admin = MasterUser::where('user', $name)->firstOrFail();

        $adminDetail->delete();
        
        $admin->delete();

        return $this->successResponse(
            statusCode: 200,
            success: true,
            msg: "Successfully remove recent admin {$name} data.",
        );
    }
}