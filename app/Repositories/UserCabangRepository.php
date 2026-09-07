<?php

namespace App\Repositories;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UserCabangRepository extends Repository implements UserCabangInterface
{
    public function getByUser(Request $request): JsonResponse
    {
        try {
            $userId = $request->query('user_id');

            if (!$userId) {
                return $this->clientErrorResponse(422, false, 'user_id wajib diisi.');
            }

            $data = DB::table('user_info_cabang')
                ->select([
                    'user_info_cabang.id',
                    'user_info_cabang.user_id',
                    'user_info_cabang.cabang_id',
                    'store_cabang.kode_cabang',
                    'store_cabang.nama_cabang',
                ])
                ->join('store_cabang', 'store_cabang.id', '=', 'user_info_cabang.cabang_id')
                ->where('user_info_cabang.user_id', $userId)
                ->whereNull('user_info_cabang.deleted_at')
                ->orderBy('store_cabang.nama_cabang', 'ASC')
                ->get();

            return $this->successResponse(200, true, 'Successfully fetch user cabangs.', $data);
        } catch (\Exception $e) {
            return $this->errorResponse(500, false, $e->getMessage());
        }
    }

    public function assignCabang(Request $request): JsonResponse
    {
        try {
            $userId    = $request->user_id;
            $cabangIds = $request->cabang_ids ?? [];

            if (!$userId || empty($cabangIds)) {
                return $this->clientErrorResponse(422, false, 'user_id dan cabang_ids wajib diisi.');
            }

            $now      = now();
            $inserted = 0;
            $skipped  = 0;

            foreach ($cabangIds as $cabangId) {
                $exists = DB::table('user_info_cabang')
                    ->where('user_id', $userId)
                    ->where('cabang_id', $cabangId)
                    ->whereNull('deleted_at')
                    ->exists();

                if ($exists) {
                    $skipped++;
                    continue;
                }

                DB::table('user_info_cabang')->insert([
                    'user_id'    => $userId,
                    'cabang_id'  => $cabangId,
                    'created_by' => 'admin',
                    'updated_by' => 'admin',
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
                $inserted++;
            }

            return $this->successResponse(
                200,
                true,
                "$inserted cabang berhasil di-assign." . ($skipped ? " $skipped dilewati (sudah ada)." : "")
            );
        } catch (\Exception $e) {
            return $this->errorResponse(500, false, $e->getMessage());
        }
    }

    public function removeCabang(int $id): JsonResponse
    {
        try {
            $row = DB::table('user_info_cabang')
                ->where('id', $id)
                ->whereNull('deleted_at')
                ->first();

            if (!$row) {
                return $this->clientErrorResponse(404, false, 'Data tidak ditemukan.');
            }

            DB::table('user_info_cabang')
                ->where('id', $id)
                ->update([
                    'deleted_at' => now(),
                    'updated_at' => now(),
                ]);

            return $this->successResponse(200, true, 'Cabang berhasil dihapus dari user.');
        } catch (\Exception $e) {
            return $this->errorResponse(500, false, $e->getMessage());
        }
    }
}
