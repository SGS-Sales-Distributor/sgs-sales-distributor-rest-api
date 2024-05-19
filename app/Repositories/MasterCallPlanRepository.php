<?php

namespace App\Repositories;

use App\Models\MasterCallPlan;
use App\Models\MasterCallPlanDetail;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class MasterCallPlanRepository extends Repository implements MasterCallPlanInterface
{   
    public function getAllData(Request $request): JsonResponse
    {
        $searchByQuery = $request->query('q');

        $masterCallPlanCache = Cache::remember(
            'masterCallPlan', 
            $this::DEFAULT_CACHE_TTL, 
            function() use ($searchByQuery)  
        {
            return MasterCallPlan::with([
                'user.type',
                'user.status',
                'details.store',
            ])
            ->when($searchByQuery, function (Builder $query) use ($searchByQuery) {
                $query->whereHas('user', function (Builder $subQuery) use ($searchByQuery) {
                    $subQuery->where('fullname', 'LIKE', '%' .$searchByQuery . '%')
                    ->orWhere('email', 'LIKE', '%' . $searchByQuery . '%');
                });
            })
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

    public function getAllDataByDateFilter(Request $request): JsonResponse
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

    public function getOneData(int $id): JsonResponse
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

    public function storeOneData(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'call_plan_id' => ['required', 'integer'],
            'month_plan' => ['required', 'integer'],
            'year_plan' => ['required', 'integer'],
            'user_id' => ['required', 'integer'],
            'store_id' => ['required', 'integer'],
            'date' => ['required', 'date'],
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

            $masterCallPlan = MasterCallPlan::create([
                'month_plan' => $request->month_plan,
                'year_plan' => $request->year_plan,
                'user_id' => $request->user_id,
            ]);

            MasterCallPlanDetail::create([
                'call_plan_id' => $request->call_plan_id,
                'store_id' => $request->store_id,
                'date' => $request->date,
            ]);

            DB::commit();

            return $this->successResponse(
                statusCode: 201, 
                success: true, 
                msg: "Successfully create new master call plan data", 
                resource: $masterCallPlan
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

    public function updateOneData(Request $request, int $id): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'call_plan_id' => ['nullable', 'integer'],
            'month_plan' => ['nullable', 'integer'],
            'year_plan' => ['nullable', 'integer'],
            'user_id' => ['required', 'integer'],
            'store_id' => ['required', 'integer'],
            'date' => ['required', 'date'],
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

        $masterCallPlanDetail = MasterCallPlanDetail::where('call_plan_id', $id)->firstOrFail();

        try {
            DB::beginTransaction();

            $masterCallPlan->update([
                'month_plan' => $request->month_plan,
                'year_plan' => $request->year_plan,
                'user_id' => $request->user_id,
            ]);

            $masterCallPlanDetail->update([
                'call_plan_id' => $request->call_plan_id,
                'store_id' => $request->store_id,
                'date' => $request->date,
            ]);

            DB::commit();

            return $this->successResponse(
                statusCode: 201, 
                success: true, 
                msg: "Successfully update master call plan {$id}", 
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

    public function removeOneData(int $id): JsonResponse
    {
        $masterCallPlanDetail = MasterCallPlanDetail::where('call_plan_id', $id)
        ->firstOrFail();

        $masterCallPlan = MasterCallPlan::where('id', $id)
        ->firstOrFail();

        $masterCallPlanDetail->delete();

        $masterCallPlan->delete();

        return $this->successResponse(
            statusCode: 200,
            success: true,
            msg: "Successfully remove master call plan {$id} and it's detail.",
        );
    }
}