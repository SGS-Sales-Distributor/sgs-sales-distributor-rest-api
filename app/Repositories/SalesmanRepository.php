<?php

namespace App\Repositories;

use App\Models\ProfilVisit;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;

class SalesmanRepository extends Repository implements SalesmanInterface
{
    public function getAll(): JsonResponse
    {
        $salesmenCache = Cache::remember('salesmen', $this::DEFAULT_CACHE_TTL, function () {
            return User::with([
                'status',
                'type',
                'visits',
                'masterCallPlans',
            ])
            ->whereHas('status')
            ->orWhereHas('type')
            ->orderBy('user_id', 'asc')
            ->paginate($this::DEFAULT_PAGINATE);
        });

        return $this->successResponse(
            statusCode: 200, 
            success: true, 
            msg: "Successfully fetch salesman.", 
            resource: $salesmenCache,
        );
    }

    public function getOne(int $userId): JsonResponse
    {
        $salesmanCache = Cache::remember("salesman:{$userId}", $this::DEFAULT_CACHE_TTL, function () use ($userId) {
            return User::with([
                'status',
                'type',
                'visits',
                'masterCallPlans',
            ])
            ->where('user_id', $userId)
            ->firstOrFail();
        });

        return $this->successResponse(
            statusCode: 200, 
            success: true, 
            msg: "Successfully fetch salesman {$userId}.", 
            resource: $salesmanCache,
        );
    }

    public function getAllVisits(int $userId): JsonResponse
    {
        $salesmanVisitsCache = Cache::remember("salesman:{$userId}:visits", $this::DEFAULT_CACHE_TTL, function () use ($userId) {
            return User::with([
                'visits',
            ])
            ->whereHas('visits')
            ->where('user_id', $userId)
            ->firstOrFail();
        });

        return $this->successResponse(
            statusCode: 200, 
            success: true, 
            msg: "Successfully fetch salesman {$userId} visits.", 
            resource: $salesmanVisitsCache,
        );
    }

    public function getOneVisit(int $userId, int $visitId): JsonResponse
    {
        $salesmanVisitCache = Cache::remember("salesman:{$userId}:visits:{$visitId}", $this::DEFAULT_CACHE_TTL, function () use ($userId, $visitId) {
            return User::with([
                'visits',
            ])
            ->whereHas('visits', function (Builder $query) use ($visitId) {
                $query->where('id', $visitId);
            })
            ->where('user_id', $userId)
            ->firstOrFail();
        });

        return $this->successResponse(
            statusCode: 200, 
            success: true, 
            msg: "Successfully fetch salesman {$userId} visit {$visitId}.", 
            resource: $salesmanVisitCache,
        );
    }

    public function getAllCallPlans(int $userId): JsonResponse
    {
        $salesmanCallPlansCache = Cache::remember("salesman:{$userId}:masterCallPlans", $this::DEFAULT_CACHE_TTL, function () use ($userId) {
            return User::with([
                'masterCallPlans',
            ])
            ->whereHas('masterCallPlans')
            ->where('user_id', $userId)
            ->firstOrFail();
        });

        return $this->successResponse(
            statusCode: 200, 
            success: true, 
            msg: "Successfully fetch salesman {$userId} call plan.", 
            resource: $salesmanCallPlansCache,
        );
    }

    public function getOneCallPlan(int $userId, int $masterCallPlanId): JsonResponse
    {
        $salesmanCallPlanCache = Cache::remember("salesman:{$userId}:masterCallPlans:{$masterCallPlanId}", $this::DEFAULT_CACHE_TTL, function () use ($userId, $masterCallPlanId) {
            return User::with([
                'masterCallPlans',
            ])
            ->whereHas('masterCallPlans', function (Builder $query) use ($masterCallPlanId) {
                $query->where('id', $masterCallPlanId);
            })
            ->where('user_id', $userId)
            ->firstOrFail();
        });

        return $this->successResponse(
            statusCode: 200, 
            success: true, 
            msg: "Successfully fetch salesman {$userId} call plan {$masterCallPlanId}.", 
            resource: $salesmanCallPlanCache,
        );
    }

    public function getAllByQuery(Request $request): JsonResponse
    {
        $searchByQuery = $request->query('q');

        $salesmanByQueryCache = Cache::remember('salesmanByQuery', $this::DEFAULT_CACHE_TTL, function () use ($searchByQuery) {
            return User::with([
                'status',
                'type',
                'visits',
                'masterCallPlans',
            ])->when($searchByQuery, function (Builder $query) use ($searchByQuery) {
                $query->where('number', 'LIKE', '%' . $searchByQuery . '%')
                ->orWhere('nik', 'LIKE', '%' . $searchByQuery . '%')
                ->orWhere('fullname', 'LIKE', '%' . $searchByQuery . '%')
                ->orWhere('email', 'LIKE', '%' . $searchByQuery . '%')
                ->orWhere('name', 'LIKE', '%' . $searchByQuery . '%');
            })
            ->orderBy('user_id', 'asc')
            ->paginate($this::DEFAULT_PAGINATE);
        });

        return $this->successResponse(
            statusCode: 200, 
            success: true, 
            msg: "Successfully fetch salesman with query %{$searchByQuery}%.", 
            resource: $salesmanByQueryCache,
        );
    }

    public function storeOne(Request $request): JsonResponse
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

            $newSalesmanAccount = User::create([
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
                resource: $newSalesmanAccount
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

            $newProfilVisit = ProfilVisit::create([
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

            $checkProfilVisit = ProfilVisit::where('id', $newProfilVisit->id)
            ->firstOrFail();

            return $this->successResponse(
                statusCode: 201, 
                success: true, 
                msg: "Successfully create new profil visit data.", 
                resource: $checkProfilVisit
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
        $validator = Validator::make($request->all(), [
            'photo_visit_out' => ['required', 'string'],
            'lat_out' => ['nullable'],
            'long_out' => ['nullable'],
        ]);
        
        if ($validator->fails()) {
            return $this->clientErrorResponse(
                statusCode: 422,
                success: false,
                msg: $validator->errors()->first(),
                resource: $validator->errors()->all(),
            );
        }

        $user = User::where('number', $userNumber)->firstOrFail();

        $latestVisit = ProfilVisit::where('id', $visitId)->firstOrFail();

        try {
            DB::beginTransaction();

            if ($latestVisit) {
                $latestVisit->update([
                    'photo_visit_out' => $request->photo_visit_out,
                    'user' => $user->fullname,
                    'tanggal_visit' => Carbon::now(env('APP_TIMEZONE'))->format('Y-m-d'),
                    'time_out' => Carbon::now(env('APP_TIMEZONE'))->format('H:i:s'),
                    'lat_out' => $request->lat_out,
                    'long_out' => $request->long_out,
                    'updated_by' => $user->fullname,
                    'updated_at' => Carbon::now(env('APP_TIMEZONE'))->format('Y-m-d H:i:s'),
                ]);
            }

            DB::commit();

            return $this->successResponse(
                statusCode: 201, 
                success: true, 
                msg: "Successfully update profil visit data related to user {$userNumber}.", 
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