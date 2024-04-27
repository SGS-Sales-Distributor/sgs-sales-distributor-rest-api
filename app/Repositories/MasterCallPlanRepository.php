<?php

namespace App\Repositories;

use App\Models\MasterCallPlan;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class MasterCallPlanRepository extends Repository implements MasterCallPlanInterface
{   
    public function getAll(): JsonResponse
    {
        $masterCallPlanCache = Cache::remember('masterCallPlan', $this::DEFAULT_CACHE_TTL, function() {
            return DB::table('master_call_plan')
                ->select('master_call_plan.*', 'user_info.*', 'master_call_plan_detail.*')
                ->join('user_info', 'master_call_plan.user_id', '=' , 'user_info.user_id')
                ->join('master_call_plan_detail', 'master_call_plan.id', '=', 'master_call_plan_detail.call_plan_id')
                ->orderBy('master_call_plan.id', 'asc')
                ->paginate(50);    
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
        $searchByQuery = $request->query('query');

        $masterCallPlanByQueryCache = Cache::remember('masterCallPlanByQuery', $this::DEFAULT_CACHE_TTL, function() use ($searchByQuery) {
            return DB::table('master_call_plan')
            ->select('master_call_plan.*', 'user_info.*', 'master_call_plan_detail.*')
            ->join('user_info', 'master_call_plan.user_id', '=' , 'user_info.user_id')
            ->join('master_call_plan_detail', 'master_call_plan.id', '=', 'master_call_plan_detail.call_plan_id')
            ->when($searchByQuery, function (QueryBuilder $query) use ($searchByQuery){
                $query->where('user_info.user_fullname', 'LIKE', '%' . $searchByQuery . '%')
                ->orWhere('user_info.user_nik', 'LIKE', '%' . $searchByQuery . '%')
                ->orWhere('user_info.user_email', 'LIKE', '%' . $searchByQuery . '%')
                ->orWhere('user_info.user_name', 'LIKE', '%' . $searchByQuery . '%');
            })
            ->orderBy('master_call_plan.id', 'asc')
            ->paginate(50);
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
        $filterByDate = $request->input('date');

        $masterCallPlanByDateFilterCache = Cache::remember('masterCallPlanByDateFilter', $this::DEFAULT_CACHE_TTL, function() use ($filterByDate) {
            return DB::table('master_call_plan')
            ->select('master_call_plan.*', 'user_info.*', 'master_call_plan_detail.*')
            ->join('user_info', 'master_call_plan.user_id', '=' , 'user_info.user_id')
            ->join('master_call_plan_detail', 'master_call_plan.id', '=', 'master_call_plan_detail.call_plan_id')
            ->when($filterByDate, function (QueryBuilder $query) use ($filterByDate){
                $query->where('master_call_plan_detail.date', '=', $filterByDate);
            })
            ->orderBy('master_call_plan.id', 'asc')
            ->paginate(50);
        });

        return $this->successResponse(
            statusCode: 200, 
            success: true, 
            msg: "Successfully fetch master call plan with date filter '{$filterByDate}'.", 
            resource: $masterCallPlanByDateFilterCache,
        );
    }

    public function getOne(int $id): JsonResponse
    {
        $masterCallPlan = MasterCallPlan::with(['user', 'details'])
            ->where('id', $id)
            ->firstOrFail();
        
        return $this->successResponse(
            statusCode: 200, 
            success: true, 
            msg: "Successfully fetch master call plan {$id}.", 
            resource: $masterCallPlan,
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