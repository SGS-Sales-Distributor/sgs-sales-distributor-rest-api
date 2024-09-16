<?php

namespace App\Repositories;

use App\Models\profilNotvisit;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ProfilNotvisitRepository extends Repository implements ProfilNotvisitInterface
{


    public function saveOneData(Request $request): JsonResponse
    {
        // return response()->json( $request->all(),200) ;
        $validator = Validator::make($request->all(), [
            'ket' => ['required', 'string'],
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

            $p_notvisit = profilNotvisit::create([
                'id_master_call_plan_detail' => $request->input('id_master_call_plan_detail', null),
                'ket' => $request->input('ket', null),
                'created_by' => $request->input('created_by', null),
                'updated_by' => $request->input('updated_by', null),
            ]);

            DB::commit();

            return $this->successResponse(
                statusCode: 201,
                success: true,
                msg: "Successfully Save Store Not Visit.",
                resource: $p_notvisit
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

    public function getOneData(int $id): JsonResponse
    {
        // DB::enableQueryLog();
        $notvisitone = DB::table('profil_notvisit')
            ->select(
                'profil_notvisit.id as idNotVisit',
                'profil_notvisit.ket as ketNotVisit',
                'profil_notvisit.created_by as userAs',
                'store_info_distri.store_name as nama_toko',
                'master_call_plan_detail.id as idPlan',
                'master_call_plan_detail.store_id as idToko',
                'master_call_plan_detail.date as tgl_plan'
            )
            ->join('master_call_plan_detail', 'profil_notvisit.id_master_call_plan_detail', '=', 'master_call_plan_detail.id')
            ->join('store_info_distri', 'master_call_plan_detail.store_id', '=', 'store_info_distri.store_id')
            ->where('profil_notvisit.id', $id)
            ->first();

        // $log = DB::getQueryLog();
        // dd($log);

        if (!$notvisitone) {
            return $this->clientErrorResponse(
                statusCode: 404,
                success: false,
                msg: "Not Visit data with id : {$id} not found.",
            );
        }

        return $this->successResponse(
            statusCode: 200,
            success: true,
            msg: "Successfully fetch Visit Tidak Terpenuhi id: {$id}.",
            resource: $notvisitone,
        );
    }
}
