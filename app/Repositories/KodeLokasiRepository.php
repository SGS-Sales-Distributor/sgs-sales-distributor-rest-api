<?php

namespace App\Repositories;

use App\Models\KodeLokasi;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class KodeLokasiRepository extends Repository implements KodeLokasiInterface
{
    public function getAllData(Request $request): JsonResponse
    {
        $kodeLokasis = DB::table('kode_lokasi')
            ->select([
                'id',
                'kode_cabang',
                'nama_cabang',
                'kode_lokasi',
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
            msg: "Berhasil",
            resource: $kodeLokasis,
            // resource: $sales,
        );
    }

    public function getOneData(string $id): JsonResponse
    {
        $kodeLokasiOne = KodeLokasi::table('kode_lokasi')
            ->select([
                'id',
                'kode_cabang',
                'nama_cabang',
                'kode_lokasi',
                'created_by',
                'updated_by',
                'created_at',
                'updated_at',
                'deleted_at',

            ])
            ->where('id', '=', $id)
            ->first();


        return $this->successResponse(
            statusCode: 200,
            success: true,
            msg: "Successfully fetch Area id= {$id}.",
            resource: $kodeLokasiOne,
        );
    }
}