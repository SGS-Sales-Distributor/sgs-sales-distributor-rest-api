<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\sts_jabatan;
use Illuminate\Support\Facades\Validator;

class StsJabatanController extends Controller
{
    public function saveStsJabatan(Request $request): JsonResponse
    {
        $validatedData = validator::make($request->all(), [
            'jabatan' => 'required|string|max:100',
            'level_atas' => 'required|string|max:100',
            'level_atas_1' => 'required|string|max:100',
            'level_atas_2' => 'required|string|max:100',
            'created_by' => 'nullable|integer'
        ]);

        if ($validatedData->fails()) {
            return response()->json(['errors' => $validatedData->errors()], 422);
        }

        try {
            $jabatan = sts_jabatan::create($request->all());
            return response()->json([
                'message' => "Berhasil Menambahkan Data",
                'data' => $jabatan
            ], 201);

        } catch (\Exception $exception) {
            return response()->json([
                'message' => 'Gagal Menambahkan Data',
                'error' => $exception
            ], 400);
        }

    }
}
