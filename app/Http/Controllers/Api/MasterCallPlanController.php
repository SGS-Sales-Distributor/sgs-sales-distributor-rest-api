<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Models\StoreCabang;
use App\Models\StoreInfoDistri;
use App\Models\User;

class MasterCallPlanController extends Controller
{
    public function getAll(Request $request): JsonResponse
    {
        return $this->masterCallPlanInterface->getAllData($request);
    }

    public function getAllByDateFilter(Request $request): JsonResponse
    {
        return $this->masterCallPlanInterface->getAllDataByDateFilter($request);
    }

    public function getOne(int $id): JsonResponse
    {
        return $this->masterCallPlanInterface->getOneData($id);
    }

    public function storeOne(Request $request): JsonResponse
    {
        return $this->masterCallPlanInterface->storeOneData($request);
    }

    public function updateOne(Request $request, int $id): JsonResponse
    {
        return $this->masterCallPlanInterface->updateOneData($request, $id);
    }

    public function removeOne(int $id): JsonResponse
    {
        return $this->masterCallPlanInterface->removeOneData($id);
    }

    public function getCboSalesmanCallplan(Request $request)
	{
		$type = (new User())->getCboSalesmanCallplan($request->cabang);
		return response()->json(
			['data' => $type],
			200
		);
	}

    public function getCboCabangCallplan(Request $request)
	{
		$type = (new StoreCabang())->getCboCabangCallplan($request->salesman);
		return response()->json(
			['data' => $type],
			200
		);
	}

    public function getCboTokoCallplan(Request $request)
	{
		$type = (new StoreInfoDistri())->getCboTokoCallplan($request->cabang);
		return response()->json(
			['data' => $type],
			200
		);
	}
}
