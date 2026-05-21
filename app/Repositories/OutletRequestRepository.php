<?php

namespace App\Repositories;

use App\Models\OutletRequest;
use App\Models\StoreInfoDistri;
use App\Models\StoreInfoDistriPerson;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class OutletRequestRepository extends Repository implements OutletRequestInterface
{
    // MD submit request outlet baru
    public function submitRequest(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'store_name'    => 'required|string|max:100',
            'store_address' => 'required|string',
            'store_phone'   => 'nullable|string|max:20',
            'store_type_id' => 'nullable|integer',
            'subcabang_id'  => 'required|integer',
            'owner'         => 'required|string|max:255',
            'nik_owner'     => 'nullable|string|max:20',
            'email_owner'   => 'nullable|string|email|max:100',
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
            $customerCode = DB::table('user_info')
                ->where('user_id', $request->userId)
                ->value('customer_code');

            $outletRequest = OutletRequest::create([
                'store_name'         => $request->store_name,
                'store_alias'        => $request->store_alias ?? $request->store_name,
                'store_address'      => $request->store_address,
                'store_phone'        => $request->store_phone ?? null,
                'store_type_id'      => $request->store_type_id ?? null,
                'subcabang_id'       => $request->subcabang_id,
                'owner'              => $request->owner,
                'nik_owner'          => $request->nik_owner ?? null,
                'email_owner'        => $request->email_owner ?? null,
                'requested_by'       => $request->userId,
                'customer_code'      => $customerCode,
                'requested_by_name'  => $request->userFullname,
                'status'             => 'pending',
                'created_by'         => $request->userId,
            ]);

            DB::commit();

            return $this->successResponse(
                statusCode: 201,
                success: true,
                msg: "Request outlet berhasil dikirim ke Supervisor.",
                resource: $outletRequest,
            );
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->errorResponse(statusCode: 500, success: false, msg: $e->getMessage());
        }
    }

    // AS lihat inbox request dari MD bawahannya
    public function getRequestsByAS(Request $request, int $userId): JsonResponse
    {
        try {
            // ambil semua MD yang atasan_id = userId (AS)
            $mdIds = DB::table('user_info')
                ->where('atasan_id', $userId)
                ->pluck('user_id');

            $requests = OutletRequest::with('cabang')
                ->whereIn('requested_by', $mdIds)
                ->orderBy('created_at', 'DESC')
                ->get();

            return $this->successResponse(
                statusCode: 200,
                success: true,
                msg: "Successfully fetch outlet requests.",
                resource: $requests,
            );
        } catch (\Exception $e) {
            return $this->errorResponse(statusCode: 500, success: false, msg: $e->getMessage());
        }
    }

    // AS approve → otomatis register outlet
    public function approveRequest(Request $request, int $id): JsonResponse
    {
        try {
            DB::beginTransaction();

            $outletRequest = OutletRequest::findOrFail($id);

            if ($outletRequest->status !== 'pending') {
                return $this->clientErrorResponse(
                    statusCode: 422,
                    success: false,
                    msg: "Request ini sudah diproses sebelumnya.",
                );
            }

            // generate store_code
            $lastId = StoreInfoDistri::max('store_id') ?? 0;
            $newId  = $lastId + 1;
            $storeCode = 'OS' . sprintf('%03d', $outletRequest->subcabang_id) . '-' . sprintf('%04d', $newId);
            // insert ke store_info_distri
            $store = StoreInfoDistri::create([
                'store_name'      => $outletRequest->store_name,
                'store_alias'     => $outletRequest->store_alias ?? $outletRequest->store_name,
                'store_address'   => $outletRequest->store_address,
                'store_phone'     => $outletRequest->store_phone,
                'store_type_id'   => $outletRequest->store_type_id,
                'subcabang_id'    => $outletRequest->subcabang_id,
                'subcabang_idnew' => $outletRequest->subcabang_id,
                'store_code'      => $storeCode,
                'customer_code'   => $outletRequest->customer_code,
                'active'          => 1,
                'created_by'      => $request->userFullname,
                'updated_by'      => $request->userFullname,
            ]);

            // insert ke store_info_distri_person
            StoreInfoDistriPerson::create([
                'store_id'    => $store->store_id,
                'owner'       => $outletRequest->owner,
                'nik_owner'   => $outletRequest->nik_owner,
                'email_owner' => $outletRequest->email_owner,
                'ktp_owner'   => '',
                'photo_other' => '',
                'created_by'  => $request->userFullname,
                'updated_by'  => $request->userFullname,
            ]);

            // update status outlet_request
            $outletRequest->update([
                'status'     => 'registered',
                'approved_by_name' => $request->userFullname,
                'updated_by' => $request->userId,
            ]);

            DB::commit();

            return $this->successResponse(
                statusCode: 200,
                success: true,
                msg: "Outlet berhasil didaftarkan.",
            );
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->errorResponse(statusCode: 500, success: false, msg: $e->getMessage());
        }
    }

    // AS reject + isi alasan
    public function rejectRequest(Request $request, int $id): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'notes' => 'required|string',
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

            $outletRequest = OutletRequest::findOrFail($id);

            if ($outletRequest->status !== 'pending') {
                return $this->clientErrorResponse(
                    statusCode: 422,
                    success: false,
                    msg: "Request ini sudah diproses sebelumnya.",
                );
            }

            $outletRequest->update([
                'status'     => 'rejected',
                'notes'      => $request->notes,
                'rejected_by_name'  => $request->userFullname,
                'updated_by' => $request->userId,
            ]);

            DB::commit();

            return $this->successResponse(
                statusCode: 200,
                success: true,
                msg: "Request outlet ditolak.",
            );
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->errorResponse(statusCode: 500, success: false, msg: $e->getMessage());
        }
    }

    public function getRequestsByMD(Request $request, int $userId): JsonResponse
    {
        try {
            $requests = OutletRequest::where('requested_by', $userId)
                ->orderBy('created_at', 'DESC')
                ->get();

            return $this->successResponse(
                statusCode: 200,
                success: true,
                msg: "Successfully fetch outlet requests.",
                resource: $requests,
            );
        } catch (\Exception $e) {
            return $this->errorResponse(statusCode: 500, success: false, msg: $e->getMessage());
        }
    }
}
