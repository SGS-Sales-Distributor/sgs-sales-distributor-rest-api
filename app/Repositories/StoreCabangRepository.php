<?php


namespace App\Repositories;

use App\Models\StoreCabang;
use App\Repositories\StoreCabangInterface;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class StoreCabangRepository extends Repository implements StoreCabangInterface
{
    public function getAllData(Request $request): JsonResponse
    {
        $storecabang = DB::table('store_cabang')
            ->select([
                'id',
                'province_id',
                'kode_cabang',
                'nama_cabang',
                'created_by',
                'updated_by',
                'created_at',
                'updated_at',
                'deleted_at',
            ])
            ->get();

        if (empty($storecabang)) {
            return $this->clientErrorResponse(
                statusCode: 422,
                success: false,
                msg: "Data Kosong",
            );
        }

        return $this->successResponse(
            statusCode: 200,
            success: true,
            msg: "Successfully fetch store Cabang.",
            resource: $storecabang,
        );
    }

    public function getCabangByUser(Request $request, $userId): JsonResponse
    {
        $storecabang = DB::table('store_cabang')
            ->select([
                'id',
                'province_id',
                'kode_cabang',
                'nama_cabang',
                'store_cabang.created_by as created_by',
                'store_cabang.updated_by as updated_by',
                'store_cabang.created_at as created_at',
                'store_cabang.updated_at as updated_at',
                'store_cabang.deleted_at as deleted_at',
            ])
            ->join('user_info', 'user_info.cabang_id', '=', 'store_cabang.id')
            ->where('user_info.user_id', '=', $userId)
            ->get();

        if (empty($storecabang)) {
            return $this->clientErrorResponse(
                statusCode: 422,
                success: false,
                msg: "Data Kosong",
            );
        }

        return $this->successResponse(
            statusCode: 200,
            success: true,
            msg: "Successfully fetch store Cabang By User.",
            resource: $storecabang,
        );
    }
}