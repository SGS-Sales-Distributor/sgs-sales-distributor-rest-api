<?php

namespace App\Repositories;

use App\Models\CabangRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class CabangRequestRepository extends Repository implements CabangRequestInterface
{
    // Sales: ambil cabang yang sudah dimiliki
    public function getMyCabangs(Request $request): JsonResponse
    {
        try {
            // GET request → ambil dari query params
            $userId = $request->userId ?? $request->query('userId');

            $cabangs = DB::table('user_info_cabang')
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

            return $this->successResponse(200, true, 'Successfully fetch my cabangs.', $cabangs);
        } catch (\Exception $e) {
            return $this->errorResponse(500, false, $e->getMessage());
        }
    }

    // Sales: ambil riwayat request milik sendiri
    public function getMyRequests(Request $request): JsonResponse
    {
        try {
            // GET request → ambil dari query params
            $userId = $request->userId ?? $request->query('userId');

            $requests = DB::table('cabang_request')
                ->select([
                    'cabang_request.id',
                    'cabang_request.cabang_id',
                    'cabang_request.kode_lokasi',
                    'cabang_request.nama_cabang',
                    'cabang_request.status',
                    'cabang_request.note',
                    'cabang_request.approved_by_name',
                    'cabang_request.rejected_by_name',
                    'cabang_request.created_at',
                ])
                ->where('cabang_request.user_id', $userId)
                ->whereNull('cabang_request.deleted_at')
                ->orderBy('cabang_request.created_at', 'DESC')
                ->get();

            return $this->successResponse(200, true, 'Successfully fetch my cabang requests.', $requests);
        } catch (\Exception $e) {
            return $this->errorResponse(500, false, $e->getMessage());
        }
    }

    // AS: ambil semua request dari MD bawahan
    public function getAllRequests(Request $request): JsonResponse
    {
        try {
            $status = $request->query('status', 'pending');

            // GET request → ambil dari query params
            $userId = $request->userId ?? $request->query('userId');

            $mdIds = DB::table('user_info')
                ->where('atasan_id', $userId)
                ->pluck('user_id');

            $requests = DB::table('cabang_request')
                ->select([
                    'cabang_request.id',
                    'cabang_request.user_id',
                    'cabang_request.cabang_id',
                    'cabang_request.kode_lokasi',
                    'cabang_request.nama_cabang',
                    'cabang_request.status',
                    'cabang_request.note',
                    'cabang_request.approved_by_name',
                    'cabang_request.rejected_by_name',
                    'cabang_request.created_at',
                    'user_info.fullname as nama_sales',
                    'user_info.phone as phone_sales',
                ])
                ->join('user_info', 'user_info.user_id', '=', 'cabang_request.user_id')
                ->whereIn('cabang_request.user_id', $mdIds)
                ->whereNull('cabang_request.deleted_at')
                ->when($status !== 'all', fn($q) => $q->where('cabang_request.status', $status))
                ->orderBy('cabang_request.created_at', 'DESC')
                ->paginate($this::DEFAULT_PAGINATE);

            return $this->successResponse(200, true, 'Successfully fetch cabang requests.', $requests);
        } catch (\Exception $e) {
            return $this->errorResponse(500, false, $e->getMessage());
        }
    }

    // Sales: kirim request cabang baru
    public function storeRequest(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'cabang_id'   => ['required', 'integer', 'exists:store_cabang,id'],
            'kode_lokasi' => ['nullable', 'string'],
            'nama_cabang' => ['required', 'string', 'max:255'],
            'userId'      => ['required'], // ← wajib dikirim dari frontend
        ]);

        if ($validator->fails()) {
            return $this->clientErrorResponse(422, false, $validator->errors()->first());
        }

        $userId = $request->userId;

        // Cek sudah punya cabang ini
        $alreadyOwned = DB::table('user_info_cabang')
            ->where('user_id', $userId)
            ->where('cabang_id', $request->cabang_id)
            ->whereNull('deleted_at')
            ->exists();

        if ($alreadyOwned) {
            return $this->clientErrorResponse(422, false, 'Anda sudah memiliki cabang ini.');
        }

        // Cek sudah ada pending request untuk cabang yang sama
        $alreadyPending = DB::table('cabang_request')
            ->where('user_id', $userId)
            ->where('cabang_id', $request->cabang_id)
            ->where('status', 'pending')
            ->whereNull('deleted_at')
            ->exists();

        if ($alreadyPending) {
            return $this->clientErrorResponse(422, false, 'Request cabang ini sudah dikirim dan sedang menunggu approval.');
        }

        try {
            DB::beginTransaction();

            $newRequest = CabangRequest::create([
                'user_id'     => $userId,
                'cabang_id'   => $request->cabang_id,
                'kode_lokasi' => $request->kode_lokasi ?? null,
                'nama_cabang' => $request->nama_cabang,
                'status'      => 'pending',
                'created_by'  => $userId,
                'updated_by'  => $userId,
            ]);

            DB::commit();

            return $this->successResponse(201, true, 'Request cabang berhasil dikirim, menunggu approval AS.', $newRequest);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->errorResponse(500, false, $e->getMessage());
        }
    }

    // AS: approve → insert ke user_info_cabang
    public function approveRequest(Request $request, int $id): JsonResponse
    {
        try {
            DB::beginTransaction();

            $cabangReq = DB::table('cabang_request')
                ->where('id', $id)
                ->where('status', 'pending')
                ->whereNull('deleted_at')
                ->first();

            if (!$cabangReq) {
                return $this->clientErrorResponse(404, false, 'Request tidak ditemukan atau sudah diproses.');
            }

            // Double check belum ada di user_info_cabang
            $alreadyOwned = DB::table('user_info_cabang')
                ->where('user_id', $cabangReq->user_id)
                ->where('cabang_id', $cabangReq->cabang_id)
                ->whereNull('deleted_at')
                ->exists();

            if (!$alreadyOwned) {
                DB::table('user_info_cabang')->insert([
                    'user_id'    => $cabangReq->user_id,
                    'cabang_id'  => $cabangReq->cabang_id,
                    'created_by' => $request->userFullname,
                    'updated_by' => $request->userFullname,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            DB::table('cabang_request')
                ->where('id', $id)
                ->update([
                    'status'           => 'approved',
                    'approved_by_name' => $request->userFullname,
                    'updated_by'       => $request->userId,
                    'updated_at'       => now(),
                ]);

            DB::commit();

            return $this->successResponse(200, true, 'Request cabang berhasil diapprove.');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->errorResponse(500, false, $e->getMessage());
        }
    }

    // AS: reject + alasan
    public function rejectRequest(Request $request, int $id): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'note' => 'required|string',
        ]);

        if ($validator->fails()) {
            return $this->clientErrorResponse(422, false, $validator->errors()->first());
        }

        try {
            DB::beginTransaction();

            $cabangReq = DB::table('cabang_request')
                ->where('id', $id)
                ->where('status', 'pending')
                ->whereNull('deleted_at')
                ->first();

            if (!$cabangReq) {
                return $this->clientErrorResponse(404, false, 'Request tidak ditemukan atau sudah diproses.');
            }

            DB::table('cabang_request')
                ->where('id', $id)
                ->update([
                    'status'           => 'rejected',
                    'note'             => $request->note,
                    'rejected_by_name' => $request->userFullname,
                    'updated_by'       => $request->userId,
                    'updated_at'       => now(),
                ]);

            DB::commit();

            return $this->successResponse(200, true, 'Request cabang berhasil ditolak.');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->errorResponse(500, false, $e->getMessage());
        }
    }
}
