<?php

namespace App\Repositories;

use App\Models\MasterCallPlan;
use App\Models\ProfilVisit;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;

class SalesmanRepository extends Repository implements SalesmanInterface
{
    public function getAllData(Request $request): JsonResponse
    {
        $searchByQuery = $request->query('q');

        $salesmenCache = Cache::remember(
            "salesmenCache", 
            $this::DEFAULT_CACHE_TTL, 
            function () use ($searchByQuery)
        {
            return User::with([
                'status',
                'type',
                'visits',
                'masterCallPlans',
            ])
            ->when($searchByQuery, function (Builder $query) use ($searchByQuery) {
                $query->where('fullname', 'LIKE', '%' . $searchByQuery . '%')
                ->orWhere('email', 'LIKE', '%' . $searchByQuery . '%');
            })
            ->orderBy('number', 'asc')
            ->paginate($this::DEFAULT_PAGINATE);
        });

        return $this->successResponse(
            statusCode: 200, 
            success: true, 
            msg: "Successfully fetch salesmen data.", 
            resource: $salesmenCache,
        );
    }

    public function getOneData(string $userNumber): JsonResponse
    {
        $salesmanCache = Cache::remember(
            "salesmen:{$userNumber}", 
            $this::DEFAULT_CACHE_TTL, 
            function () use ($userNumber) 
        {
            return User::with([
                'status',
                'type',
                'visits',
                'masterCallPlans',
            ])
            ->where('number', $userNumber)
            ->firstOrFail();
        });

        return $this->successResponse(
            statusCode: 200, 
            success: true, 
            msg: "Successfully fetch salesman {$userNumber} data.", 
            resource: $salesmanCache,
        );
    }

    public function getVisitsData(string $userNumber): JsonResponse
    {
        $salesmanVisitsCache = Cache::remember(
            "salesmen:{$userNumber}:visits", 
            $this::DEFAULT_CACHE_TTL, 
            function () use ($userNumber) 
        {
            return ProfilVisit::whereHas("user", function (Builder $query) use ($userNumber) {
                $query->where('number', $userNumber);
            })
            ->orderBy('user', 'asc')
            ->get();
        });

        return $this->successResponse(
            statusCode: 200, 
            success: true, 
            msg: "Successfully fetch salesman {$userNumber} visits data.", 
            resource: $salesmanVisitsCache,
        );
    }

    public function getOneVisitData(string $userNumber, int $visitId): JsonResponse
    {
        $salesmanVisitCache = Cache::remember(
            "salesmen:{$userNumber}:visits:{$visitId}", 
            $this::DEFAULT_CACHE_TTL, 
            function () use ($userNumber, $visitId) 
        {
            return ProfilVisit::whereHas("user", function (Builder $query) use ($userNumber, $visitId) {
                $query->where('number', $userNumber);
            })->where('id', $visitId)
            ->firstOrFail();
        });

        return $this->successResponse(
            statusCode: 200, 
            success: true, 
            msg: "Successfully fetch salesman {$userNumber} visit {$visitId} data.", 
            resource: $salesmanVisitCache,
        );
    }

    public function getCallPlansData(string $userNumber): JsonResponse
    {
        $salesmanCallPlansCache = Cache::remember(
            "salesmen:{$userNumber}:callPlans", 
            $this::DEFAULT_CACHE_TTL, 
            function () use ($userNumber) 
        {
            return MasterCallPlan::whereHas("user", function (Builder $query) use ($userNumber) {
                $query->where('number', $userNumber);
            })->orderBy('id', 'asc')
            ->get();
        });

        return $this->successResponse(
            statusCode: 200, 
            success: true, 
            msg: "Successfully fetch salesman {$userNumber} call plans data.", 
            resource: $salesmanCallPlansCache,
        );
    }

    public function getOneCallPlanData(string $userNumber, int $callPlanId): JsonResponse
    {
        $salesmanCallPlanCache = Cache::remember(
            "salesmen:{$userNumber}:callPlans:{$callPlanId}", 
            $this::DEFAULT_CACHE_TTL, 
            function () use ($userNumber, $callPlanId) 
        {
            return User::with([
                'masterCallPlans',
            ])
            ->whereHas('masterCallPlans', function (Builder $query) use ($callPlanId) {
                $query->where('id', $callPlanId);
            })
            ->where('user_id', $userNumber)
            ->firstOrFail();
        });

        return $this->successResponse(
            statusCode: 200, 
            success: true, 
            msg: "Successfully fetch salesman {$userNumber} call plan {$callPlanId} data.", 
            resource: $salesmanCallPlanCache,
        );
    }

    public static function str_random($length = 16)
    {
        $pool = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';

        return substr(str_shuffle(str_repeat($pool, 5)), 0, $length);
    }

    public function fileUploadGambar(Request $request, string $userNumber): JsonResponse
    {
        //die(print_r($request));
        DB::beginTransaction();

        // return response()->json([
        //     'status' => true,
        //     'message' => app()->basePath('app'),
        // ], 201);
        //try {
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            //$name = time() . '.' . $image->getClientOriginalExtension();
            $name = date('YmdHis') . '_' . $this->str_random(10) . '.' . 'png';
            //$destinationPath = storage_path();
            $destinationPath = public_path('images');
            $image->move($destinationPath, $name);



            // $compress_file = "compress_" . $name;
            // $compressed_img = $destinationPath . '/' . $compress_file;
            // $file_name = $destinationPath . '/' . $name;
            // $compress_image = $this->compress($file_name, $compressed_img);
            //unlink($file_name);
            //Image::make($image->getRealPath())->resize(150, 150)->save($path);

            //return response()->json(['data' => "image is uploaded"]);

            $user = User::where('number', $userNumber)->firstOrFail();
            
            $checkInVisit = ProfilVisit::create([
                'store_id' => $request->store_id,
                'user' => $user->fullname,
                'photo_visit' => $name,
                'tanggal_visit' => Carbon::now(env('APP_TIMEZONE'))->format('Y-m-d'),
                'time_in' => Carbon::now(env('APP_TIMEZONE'))->format('H:i:s'),
                'lat_in' => $request->lat_in,
                'long_in' => $request->long_in,
                'created_by' => $user->fullname,
                'updated_by' => $user->fullname,
            ]);

            DB::commit();

            return $this->successResponse(
                statusCode: 201, 
                success: true, 
                msg: "Successfully check-in visit related to salesman {$userNumber}.", 
                resource: $checkInVisit
            );
            //return successful response
            // return response()->json([
            //     'status' => true,
            //     'message' => 'Photo Upload successfully. (1)',
            // ], 201);
        } else {

            return response()->json([
                'status' => false,
                'message' => 'failed upload photo (2a).',
            ], 409);

            // // DB::rollback();
            // // //return error message
            // // return response()->json([
            // //     'status' => false,
            // //     'message' => $request,
            // // ], 409);


            // $image = $request->image;  // your base64 encoded
            // $image = str_replace('data:image/png;base64,', '', $image);
            // $image = str_replace(' ', '+', $image);
            // $imageName = $this->str_random(10) . '.' . 'png';
            // File::put(storage_path() . '/' . $imageName, base64_decode($image));

            // // $data = DB::select("
            // //         INSERT INTO test_photo(url) 
            // //             VALUES ('" . $imageName . "')");


            // DB::commit();
            // //return successful response
            // return response()->json([
            //     'status' => true,
            //     'message' => 'Photo Upload successfully. (2)',
            //     'request' => $request,
            // ], 201);
        }
        //} catch (\Exception $e) {
        DB::rollback();
        //return error message
        return response()->json([
            'status' => false,
            'message' => 'failed upload photo (2b).',
        ], 409);
        //}
    }

    public function storeOneData(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'nik' => ['required', 'string', 'max:20'],
            'fullname' => ['required', 'string', 'max:200'],
            'phone' => ['required', 'string', 'max:20', 'unique:user_info,phone'],
            'email' => ['required', 'string', 'email', 'lowercase', 'max:255', 'unique:user_info,email'],
            'name' => ['required', 'string', 'max:50', 'unique:user_info,name'],
            'password' => ['required', 'max:100', 'confirmed', Password::default()]
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

            $salesman = User::create([
                'number' => $this->randomDigitNumber->generateRandomNumber(),
                'nik' => $request->nik,
                'fullname' => $request->fullname,
                'phone' => $request->phone,
                'email' => $request->email,
                'name' => $request->name,
                'password' => $request->password,
            ]);

            DB::commit();

            return $this->successResponse(
                statusCode: 201, 
                success: true, 
                msg: "Successfully create new salesman account.", 
                resource: $salesman
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

    public function checkInVisit(Request $request, string $userNumber): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'store_id' => ['nullable', 'integer'],
            'photo_visit' => ['required', 'string'],
            'lat_in' => ['nullable'],
            'long_in' => ['nullable'],
        ]);
        
        if ($validator->fails()) {
            return $this->clientErrorResponse(
                statusCode: 422,
                success: false,
                msg: $validator->errors()->first(),
                resource: $validator->errors()->all(),
            );
        }

        $user = User::where('number', $userNumber)
        ->firstOrFail();

        try {
            DB::beginTransaction();

            $checkInVisit = ProfilVisit::create([
                'store_id' => $request->store_id,
                'user' => $user->fullname,
                'photo_visit' => $request->photo_visit,
                'tanggal_visit' => Carbon::now(env('APP_TIMEZONE'))->format('Y-m-d'),
                'time_in' => Carbon::now(env('APP_TIMEZONE'))->format('H:i:s'),
                'lat_in' => $request->lat_in,
                'long_in' => $request->long_in,
                'created_by' => $user->fullname,
                'updated_by' => $user->fullname,
            ]);

            DB::commit();

            return $this->successResponse(
                statusCode: 201, 
                success: true, 
                msg: "Successfully check-in visit related to salesman {$userNumber}.", 
                resource: $checkInVisit
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

    public function checkOutVisit(Request $request, string $userNumber, int $visitId): JsonResponse
    {
                // $validator = Validator::make($request->all(), [
        //     'photo_visit_out' => ['required', 'string'],
        //     'lat_out' => ['nullable'],
        //     'long_out' => ['nullable'],
        // ],
        // [
        //     'required' => ':attribute is required!',
        //     'unique' => ':attribute is unique field!',
        //     'min' => ':attribute should be :min in characters',
        //     'max' => ':attribute could not more than :max characters',
        //     'confirmed' => ':attribute confirmation does not match!',  
        // ]);
        
        // if ($validator->fails()) {
        //     return $this->clientErrorResponse(
        //         statusCode: 422,
        //         success: false,
        //         msg: $validator->errors()->first(),
        //         resource: $validator->errors()->all(),
        //     );
        // }

        try {
            DB::beginTransaction();

            if ($request->hasFile('image')) {
                $image = $request->file('image');
                //$name = time() . '.' . $image->getClientOriginalExtension();
                $name = date('YmdHis') . '_' . $this->str_random(10) . '.' . 'png';
                //$destinationPath = storage_path();
                $destinationPath = public_path('images');
                $image->move($destinationPath, $name);

                $salesman = User::where('number', $userNumber)->firstOrFail();

                $latestVisit = ProfilVisit::where('id', $visitId)->firstOrFail();
    
                $latestVisit->update([
                    'photo_visit_out' => $name,
                    'user' => $salesman->fullname,
                    'tanggal_visit' => Carbon::now(env('APP_TIMEZONE'))->format('Y-m-d'),
                    'time_out' => Carbon::now(env('APP_TIMEZONE'))->format('H:i:s'),
                    'lat_out' => $request->lat_out,
                    'long_out' => $request->long_out,
                    'updated_by' => $salesman->fullname,
                    'updated_at' => Carbon::now(env('APP_TIMEZONE'))->format('Y-m-d H:i:s'),
                ]);
                
            }else{
                return response()->json([
                    'status' => false,
                    'message' => 'failed upload photo (2b).',
                ], 409);

            }

            DB::commit();

            return $this->successResponse(
                statusCode: 201, 
                success: true, 
                msg: "Successfully check-out visit related to salesman {$userNumber}.", 
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

    public function updateOneData(Request $request, string $userNumber): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'number' => ['nullable', 'string', 'max:10'],
            'nik' => ['nullable', 'string', 'max:20', 'unique:user_info,nik'],
            'fullname' => ['nullable', 'string', 'max:200'],
            'phone' => ['nullable', 'string', 'max:20', 'unique:user_info,phone'],
            'email' => ['nullable', 'string', 'max:255', 'unique:user_info,email', 'lowercase', 'email'],
            'name' => ['nullable', 'string', 'max:50', 'unique:user_info,name'],
            'type_id' => ['nullable', 'integer', 'max_digits:10'],
            'status' => ['nullable', 'integer', 'max_digits:10'],
            'cabang_id' => ['nullable', 'integer', 'max_digits:10'],
            'store_id' => ['nullable', 'integer', 'max_digits:10'],
            'status_ba' => ['nullable', 'string', 'max:50'], 
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
                resource: $validator->errors()->all(),
            );
        }

        $salesman = User::where('number', $userNumber)->firstOrFail();

        try {
            DB::beginTransaction();

            $salesman->update([
                'number' => $request->number,
                'nik' => $request->nik,
                'fullname' => $request->fullname,
                'phone' => $request->phone,
                'email' => $request->email,
                'name' => $request->name,
                'type_id' => $request->type_id,
                'status' => $request->status,
                'cabang_id' => $request->cabang_id,
                'store_id' => $request->store_id,
                'status_ba' => $request->status_ba,
            ]);

            DB::commit();

            return $this->successResponse(
                statusCode: 201, 
                success: true, 
                msg: "Successfully update recent salesman {$userNumber} data.", 
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

    public function updateProfileData(Request $request, string $userNumber): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'number' => ['nullable', 'string', 'max:10'],
            'nik' => ['nullable', 'string', 'max:20', 'unique:user_info,nik'],
            'fullname' => ['nullable', 'string', 'max:200'],
            'phone' => ['nullable', 'string', 'max:20', 'unique:user_info,phone'],
            'email' => ['nullable', 'string', 'max:255', 'unique:user_info,email', 'lowercase', 'email'],
            'name' => ['nullable', 'string', 'max:50', 'unique:user_info,name'],
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
                resource: $validator->errors()->all(),
            );
        }

        $salesman = User::where('number', '=', $userNumber)->firstOrFail();

        try {
            DB::beginTransaction();

            $salesman->update([
                'number' => $request->number,
                'nik' => $request->nik,
                'fullname' => $request->fullname,
                'phone' => $request->phone,
                'email' => $request->email,
                'name' => $request->name, 
            ]);

            DB::commit();

            return $this->successResponse(
                statusCode: 201, 
                success: true, 
                msg: "Successfully update recent salesman {$userNumber} profile data.", 
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

    public function changePasswordData(Request $request, string $userNumber): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'current_password' => ['required', 'string'],
            'password' => ['required', 'max:100', 'confirmed', Password::default()],
        ]);

        if ($validator->fails()) {
            return $this->clientErrorResponse(
                statusCode: 422,
                success: false,
                msg: $validator->errors()->first(),
                resource: $validator->errors()->all(),
            );
        }

        $salesman = User::where('number', $userNumber)->firstOrFail();

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
                    msg: "Successfully update salesman {$userNumber} password data.",
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

    public function removeOneData(string $userNumber): JsonResponse
    {
        $salesman = User::where('number', $userNumber)->firstOrFail();

        $salesman->delete();

        return $this->successResponse(
            statusCode: 200, 
            success: true, 
            msg: "Successfully remove recent salesman {$userNumber} data.", 
        );
    }
}