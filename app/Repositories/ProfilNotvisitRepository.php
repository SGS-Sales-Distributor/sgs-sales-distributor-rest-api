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

class ProfilNotvisitRepository extends Repository implements ProfilNotvisitInterface{


    public function saveOneData(Request $request): JsonResponse{
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
}
