<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Repositories\MasterCustomerInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

class MasterCustomerController extends Controller
{
    private $masterCustomerInterface;

    public function __construct(MasterCustomerInterface $masterCustomerInterface)
    {
        $this->masterCustomerInterface = $masterCustomerInterface;
    }

    public function getAll(Request $request): JsonResponse
    {
        return $this->masterCustomerInterface->getAllData($request);
    }

    public function getOne(string $id): JsonResponse
    {
        return $this->masterCustomerInterface->getOneData($id);
    }

    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'customer_name'     => 'required|string|max:100',
            'customer_address'  => 'required|string|max:512',
            'customer_pos_code' => 'nullable|string|max:10',
            'prefix'            => 'nullable|string|max:10',
            'unit_code'         => 'nullable|string|max:20',
            'status'            => 'nullable|integer|in:0,1',
            'customer_code'     => 'nullable|string|max:20|unique:mst_customer,customer_code',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'msg'     => 'Validasi Gagal',
                'errors'  => $validator->errors()
            ], 422);
        }

        return $this->masterCustomerInterface->createData($request);
    }

    public function update(Request $request, string $id): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'customer_name'     => 'required|string|max:100',
            'customer_address'  => 'required|string|max:512',
            'customer_pos_code' => 'nullable|string|max:10',
            'prefix'            => 'nullable|string|max:10',
            'unit_code'         => 'nullable|string|max:20',
            'status'            => 'nullable|integer|in:0,1',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'msg'     => 'Validasi Gagal',
                'errors'  => $validator->errors()
            ], 422);
        }

        return $this->masterCustomerInterface->updateData($request, $id);
    }

    public function destroy(string $id): JsonResponse
    {
        return $this->masterCustomerInterface->deleteData($id);
    }
}