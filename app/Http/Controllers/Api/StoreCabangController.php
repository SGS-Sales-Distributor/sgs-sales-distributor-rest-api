<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Models\StoreCabang;
use App\Models\PublicModel;
use Illuminate\Support\Facades\URL;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;

class StoreCabangController extends Controller
{
	public function paging(Request $request)
	{
		$URL = URL::current();

		if (!isset($request->search)) {
			$count = (new StoreCabang())->count();
			// $arr_pagination = (new PublicModel())->pagination_without_search($URL, $request->limit, $request->offset);
			$arr_pagination = (new PublicModel())->paginateDataWithoutSearchQuery($URL, $request->limit, $request->offset);
			$todos = (new StoreCabang())->get_data_($request->search, $arr_pagination);
		} else {
			$arr_pagination = (new PublicModel())->paginateDataWithoutSearchQuery($URL, $request->limit, $request->offset);
			// $arr_pagination = (new PublicModel())->pagination_without_search($URL, $request->limit, $request->offset, $request->search);
			$todos = (new StoreCabang())->get_data_($request->search, $arr_pagination);
			$count = $todos->count();
		}

		return response()->json(
			// (new PublicModel())->array_respon_200_table($todos, $count, $arr_pagination),
			(new PublicModel())->array_respon_200_table_tr($todos, $count, $arr_pagination),
			200
		);
	}
	public function getAll(Request $request): JsonResponse
	{
		return $this->StoreCabangInterface->getAllData($request);
	}

	public function getCabangByUser(Request $request, int $userId): JsonResponse
	{
		return $this->StoreCabangInterface->getCabangByUser($request,$userId);
	}
}
