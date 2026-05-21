<?php

namespace App\Repositories;

use App\Models\MasterCustomer;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class MasterCustomerRepository extends Repository implements MasterCustomerInterface
{
    public function getAllData(Request $request): JsonResponse
    {
        $search = $request->query('search');

        $customers = DB::table('mst_customer')
            ->select([
                'id',
                'customer_code',
                'customer_name',
                'customer_address',
                'customer_pos_code',
                'status',
                'prefix',
                'unit_code',
                'created_by',
                'updated_at'
            ])
            ->whereNull('deleted_at')
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('customer_name', 'LIKE', '%' . $search . '%')
                        ->orWhere('customer_code', 'LIKE', '%' . $search . '%');
                });
            })
            ->get();

        if ($customers->isEmpty()) {
            return $this->clientErrorResponse(
                statusCode: 404,
                success: false,
                msg: 'Data Customer Kosong',
            );
        }

        return $this->successResponse(
            statusCode: 200,
            success: true,
            msg: "Berhasil mengambil data customer.",
            resource: $customers,
        );
    }

    public function getOneData(string $id): JsonResponse
    {
        $customer = DB::table('mst_customer')
            ->where('id', $id)
            ->whereNull('deleted_at')
            ->first();

        if (!$customer) {
            return $this->clientErrorResponse(404, false, "Data tidak ditemukan");
        }

        return $this->successResponse(
            statusCode: 200,
            success: true,
            msg: "Berhasil mengambil customer id={$id}.",
            resource: $customer,
        );
    }

    public function getCode(): string
    {
        $lastCode = MasterCustomer::withTrashed()
            ->where('customer_code', 'LIKE', 'MC%')
            ->orderByRaw("CAST(SUBSTRING(customer_code FROM 3) AS INTEGER) DESC")
            ->value('customer_code');

        if ($lastCode) {
            $lastNumber = (int) substr($lastCode, 2);
            $nextNumber = $lastNumber + 1;
        } else {
            $nextNumber = 1;
        }

        $maxAttempts = 100;
        $attempt = 0;

        do {
            $code = 'MC' . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
            $nextNumber++;
            $attempt++;
        } while (
            MasterCustomer::withTrashed()
            ->where('customer_code', $code)
            ->exists()
            && $attempt < $maxAttempts
        );

        return $code;
    }

    public function createData(Request $request): JsonResponse
    {
        try {
            $code = $request->customer_code ?? $this->getCode();

            $data = [
                'customer_code'     => $code,
                'customer_name'     => $request->customer_name,
                'customer_address'  => $request->customer_address,
                'customer_pos_code' => $request->customer_pos_code,
                'status'            => $request->status ?? 0,
                'prefix'            => $request->prefix,
                'unit_code'         => $request->unit_code,
                'created_by'        => 1,
                'created_at'        => now(),
            ];

            $id = DB::table('mst_customer')->insertGetId($data);

            $data['id'] = $id;

            return response()->json([
                'success' => true,
                'data' => $data
            ]);
        } catch (\Exception $e) {

            return response()->json([
                'message' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile(),
                'trace' => $e->getTraceAsString()
            ], 500);
        }
    }

    public function updateData(Request $request, string $id): JsonResponse
    {
        $check = DB::table('mst_customer')->where('id', $id)->whereNull('deleted_at')->exists();
        if (!$check) return $this->clientErrorResponse(404, false, "Data tidak ditemukan");

        $updateData = [
            'customer_name'     => $request->customer_name,
            'customer_address'  => $request->customer_address,
            'updated_by'        => Auth::id() ?? 0,
            'updated_at'        => now(),
        ];

        // Field opsional: hanya update jika dikirim dalam request
        if ($request->has('customer_pos_code')) {
            $updateData['customer_pos_code'] = $request->customer_pos_code;
        }
        if ($request->has('prefix')) {
            $updateData['prefix'] = $request->prefix;
        }
        if ($request->has('unit_code')) {
            $updateData['unit_code'] = $request->unit_code;
        }
        if ($request->has('status')) {
            $updateData['status'] = $request->status;
        }

        DB::table('mst_customer')->where('id', $id)->update($updateData);

        return $this->successResponse(200, true, "Customer berhasil diperbarui");
    }

    public function deleteData(string $id): JsonResponse
    {
        $check = DB::table('mst_customer')->where('id', $id)->whereNull('deleted_at')->exists();
        if (!$check) return $this->clientErrorResponse(404, false, "Data tidak ditemukan");

        DB::table('mst_customer')->where('id', $id)->update([
            'deleted_at' => now(),
            'deleted_by' => Auth::id() ?? 0
        ]);

        return $this->successResponse(200, true, "Customer berhasil dihapus");
    }
}
