<?php

namespace App\Http\Controllers\Api;

use App\Models\MasterCallPlanDetail;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class MasterCallPlanController extends Controller
{
    public function getAll(Request $request): JsonResponse
    {
        return $this->masterCallPlanInterface->getAllData($request);
    }

    public function getAllByDateFilter(Request $request): JsonResponse
    {
        return $this->masterCallPlanInterface->getAllDataByDateFilter($request);
    }

    public function getOne(int $id): JsonResponse
    {
        return $this->masterCallPlanInterface->getOneData($id);
    }

    public function storeOne(Request $request): JsonResponse
    {
        return $this->masterCallPlanInterface->storeOneData($request);
    }

    public function updateOne(Request $request, int $id): JsonResponse
    {
        return $this->masterCallPlanInterface->updateOneData($request, $id);
    }

    public function notVisitedUser(Request $request, int $userId): JsonResponse {
        return $this->masterCallPlanInterface->notVisitedUsers($request, $userId);
    } 

    public function removeOne(int $id): JsonResponse
    {
        return $this->masterCallPlanInterface->removeOneData($id);
    }

    public function getOneDetail($id)
    {
        $data = MasterCallPlanDetail::find($id);
        // $masterCallPlanCache = Cache::remember(
        //     "masterCallPlan:{$id}",
        //     $this::DEFAULT_CACHE_TTL,
        //     function () use ($id) {
        //         return MasterCallPlan::with(['user', 'details'])
        //             ->where('id', $id)
        //             ->firstOrFail();
        //     }
        // );

        return $this->successResponse(
            statusCode: 200,
            success: true,
            msg: "Successfully fetch master call plan detail {$id}.",
            resource: $data,
        );

    }

    public function updateDetail(Request $request, $id)
    {
        try {
            //    $getShowStock= $this->getOne( $id, $request);

            DB::beginTransaction();

            $queryUpdate = MasterCallPlanDetail::findOrFail($id);
            // $queryUpdate= Stock::where('id', $id)->firstOrFail();


            $queryUpdate->update([
                'date' => $request->tanggal,
                'store_id' => $request->store_id,
                'updated_by' => $request->updated_by, //siapa yg update
                'updated_at' => Carbon::now(env('APP_TIMEZONE'))->format('Y-m-d H:i:s'), //update time
            ]);

            DB::commit();

            return $this->successResponse(
                statusCode: 200,
                success: true,
                msg: 'Call Plan Detail berhasil diupdate nih',
                resource: $queryUpdate
            );
        } catch (\Exception $error) {
            return $this->errorResponse(
                statusCode: 500,
                success: false,
                msg: $error->getMessage(),
            );
        }
    }

    public function removeDetailCallPlan($id)
    {
        $masterCallPlanDetail = MasterCallPlanDetail::where('id', $id)
            ->firstOrFail();

        $masterCallPlanDetail->delete();

        return $this->successResponse(
            statusCode: 200,
            success: true,
            msg: "Successfully remove master call plan {$id} and it's detail.",
        );
    }
}
