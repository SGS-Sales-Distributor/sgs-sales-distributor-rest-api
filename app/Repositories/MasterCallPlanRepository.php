<?php

namespace App\Repositories;

use App\Models\MasterCallPlan;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class MasterCallPlanRepository extends Repository implements MasterCallPlanInterface
{   
    public function getAll(): JsonResponse
    {
        $masterCallPlanCache = Cache::remember(
            'masterCallPlan', 
            $this::DEFAULT_CACHE_TTL, 
            function() 
        {
            return MasterCallPlan::with([
                'user',
                'details',
            ])
            ->orderBy('id', 'asc')
            ->paginate($this::DEFAULT_PAGINATE);
        });

        return $this->successResponse(
            statusCode: 200, 
            success: true, 
            msg: "Successfully fetch master call plan.", 
            resource: $masterCallPlanCache,
        );
    }

    public function getAllByQuery(Request $request): JsonResponse
    {
        $searchByQuery = $request->query('q');

        $masterCallPlanByQueryCache = Cache::remember(
            'masterCallPlanByQuery', 
            $this::DEFAULT_CACHE_TTL, 
            function() use ($searchByQuery) 
        {
            return MasterCallPlan::with([
                'user',
                'details',
            ])
            ->when($searchByQuery, function (Builder $query) use ($searchByQuery) {
                $query->whereHas('user', function (Builder $subQuery) use ($searchByQuery) {
                    $subQuery->where('user_fullname', 'LIKE', '%' .$searchByQuery . '%')
                    ->orWhere('user_nik', 'LIKE', '%' . $searchByQuery . '%')
                    ->orWhere('user_email', 'LIKE', '%' . $searchByQuery . '%')
                    ->orWhere('user_name', 'LIKE', '%' . $searchByQuery . '%');
                });
            })
            ->orderBy('id', 'asc')
            ->paginate($this::DEFAULT_PAGINATE);
        });

        return $this->successResponse(
            statusCode: 200, 
            success: true, 
            msg: "Successfully fetch master call plan with query %{$searchByQuery}%.", 
            resource: $masterCallPlanByQueryCache,
        );
    }

    public function getAllByDateFilter(Request $request): JsonResponse
    {
        $searchByDateQuery = $request->query('q');

        $filterByDateRange = $this->dateRangeFilter->parseDateRange($searchByDateQuery);

        $filterByDate = $this->dateRangeFilter->parseDate($searchByDateQuery);

        $filterByYearRange = $this->dateRangeFilter->parseYearRange($searchByDateQuery);

        $filterByYear = $this->dateRangeFilter->parseYear($searchByDateQuery);

        $masterCallPlanByDateFilterCache = Cache::remember(
            'masterCallPlanByDateFilter', 
            $this::DEFAULT_CACHE_TTL, 
            function() use (
                $filterByDateRange, 
                $filterByDate,
                $filterByYearRange,
                $filterByYear,
            ) 
        {
            if ($filterByDateRange)
            {
                return MasterCallPlan::with([
                    'user', 
                    'details'
                ])
                ->when($filterByDateRange, function (Builder $query) use ($filterByDateRange) {
                    $query->whereHas('details', function (Builder $subQuery) use ($filterByDateRange) {
                        $subQuery->whereBetween('date', $filterByDateRange);
                    });
                })
                ->orderBy('id', 'asc')
                ->paginate($this::DEFAULT_PAGINATE);
            }

            if ($filterByDate)
            {
                return MasterCallPlan::with([
                    'user', 
                    'details'
                ])
                ->when($filterByDate, function (Builder $query) use ($filterByDate) {
                    $query->whereHas('details', function (Builder $subQuery) use ($filterByDate) {
                        $subQuery->whereDate('date', '=', $filterByDate);
                    });
                })
                ->orderBy('id', 'asc')
                ->paginate($this::DEFAULT_PAGINATE);
            }

            if ($filterByYearRange)
            {
                return MasterCallPlan::with([
                    'user', 
                    'details'
                ])
                ->when($filterByYearRange, function (Builder $query) use ($filterByYearRange) {
                    $query->whereHas('details', function (Builder $subQuery) use ($filterByYearRange) {
                        $subQuery->whereBetween('date', $filterByYearRange);
                    });
                })
                ->orderBy('id', 'asc')
                ->paginate($this::DEFAULT_PAGINATE);
            }

            if ($filterByYear)
            {
                return MasterCallPlan::with([
                    'user', 
                    'details'
                ])
                ->when($filterByYear, function (Builder $query) use ($filterByYear) {
                    $query->whereHas('details', function (Builder $subQuery) use ($filterByYear) {
                        $subQuery->whereYear('date', $filterByYear);
                    });
                })
                ->orderBy('id', 'asc')
                ->paginate($this::DEFAULT_PAGINATE);
            }
        });

        return $this->successResponse(
            statusCode: 200, 
            success: true, 
            msg: "Successfully fetch master call plan with date filter {$request->input('q')}", 
            resource: $masterCallPlanByDateFilterCache,
        );
    }

    public function getOne(int $id): JsonResponse
    {
        $masterCallPlanCache = Cache::remember(
            "masterCallPlan:{$id}", 
            $this::DEFAULT_CACHE_TTL, 
            function () use ($id) 
        {
           return MasterCallPlan::with(['user', 'details'])
           ->where('id', $id)
           ->firstOrFail();  
        });
        
        return $this->successResponse(
            statusCode: 200, 
            success: true, 
            msg: "Successfully fetch master call plan {$id}.", 
            resource: $masterCallPlanCache,
        );
    }

    public function storeOne(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'month_plan' => ['nullable', 'integer', 'max_digits:15'],
            'year_plan' => ['nullable', 'integer', 'max:digits:15'],
            'user_id' => ['required', 'integer', 'max_digits:20'],
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

            $newMasterCallPlan = MasterCallPlan::create([
                'month_plan' => $request->month_plan,
                'year_plan' => $request->year_plan,
                'user_id' => $request->user_id,
            ]);

            DB::commit();

            $checkMasterCallPlan = MasterCallPlan::where('id', $newMasterCallPlan->id)
            ->firstOrFail();

            return $this->successResponse(
                statusCode: 201, 
                success: true, 
                msg: "Successfully create new master call plan data", 
                resource: $checkMasterCallPlan
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

    public function updateOne(Request $request, int $id): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'month_plan' => ['nullable', 'integer', 'max_digits:15'],
            'year_plan' => ['nullable', 'integer', 'max:digits:15'],
            'user_number' => ['required', 'integer', 'max_digits:20'],
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

        $masterCallPlan = MasterCallPlan::where('id', $id)->firstOrFail();

        try {
            DB::beginTransaction();

            $masterCallPlan->update([
                'month_plan' => $request->month_plan,
                'year_plan' => $request->year_plan,
                'user_id' => $request->user_id,
            ]);

            DB::commit();

            return $this->successResponse(
                statusCode: 201, 
                success: true, 
                msg: "Successfully update master call plan {$id}", 
                resource: $masterCallPlan,
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

    public function removeOne(int $id): JsonResponse
    {
        $masterCallPlan = MasterCallPlan::where('id', $id)->findOrFail();

        $masterCallPlan->delete();

        return $this->successResponse(
            statusCode: 200,
            success: true,
            msg: "Successfully remove master call plan {$id}.",
        );
    }
}