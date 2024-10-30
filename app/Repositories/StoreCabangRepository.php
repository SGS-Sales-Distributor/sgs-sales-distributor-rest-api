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

        return $this->successResponse(
            statusCode: 200,
            success: true,
            msg: "Successfully fetch store Cabang.",
            resource: $storecabang,
        );
    }
}