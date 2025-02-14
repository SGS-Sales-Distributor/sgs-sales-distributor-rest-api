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
        // DB::enableQueryLog();
        $kodeLokasis = DB::table('kode_lokasi')
            ->select([
                'kode_lokasi.id as id',
                'kode_lokasi.kode_cabang as kode_cabang',
                'kode_lokasi.nama_cabang as nama_cabang',
                'kode_lokasi',
                'kode_lokasi.created_by as created_by',
                'kode_lokasi.updated_by as updated_by',
                'kode_lokasi.created_at as created_at',
                'kode_lokasi.updated_at as updated_at',
                'kode_lokasi.deleted_at as deleted_at',
                'store_cabang.id as idCabang',
                'store_cabang.kode_cabang as kdCabang',
                'store_cabang.nama_cabang as nmCabang',
            ])
            ->join('store_cabang','store_cabang.id','=','kode_lokasi.cabang_id')
            ->get();
        // $log = DB::getQueryLog();
        // dd($log);

        if (!$kodeLokasis) {
            return $this->clientErrorResponse(
                statusCode: 422,
                success: false,
                msg: 'Data Kosong',
            );
        }


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