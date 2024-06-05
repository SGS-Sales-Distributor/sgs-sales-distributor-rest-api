<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ProfilVisit;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ProfilVisitController extends Controller
{
	public function getAll(Request $request)
	{
		$searchByQuery = $request->query('q');

		$offsetQuery = $request->query('offset');

		$limitQuery = $request->query('limit');

		// $visits = DB::table('profil_visit')
		// 	->select('profil_visit.*', 'user_info.*')
		// 	->join('user_info', 'profil_visit.user', '=', 'user_info.user_id')
		// 	->first();


		// $visits = ProfilVisit::with('store')->get();
		$visits = ProfilVisit::with(['user', 'store'])->when($searchByQuery, function (Builder $query) use ($searchByQuery) {
			$query->where('user', 'LIKE', '%' . $searchByQuery . '%');
		})->orderBy('id', 'asc')
			->paginate(50);

		return $this->successResponse(
			statusCode: 200,
			success: true,
			msg: "Successfully fetch visits data.",
			resource: $visits,
		);
	}


	// public function index()
	// {
	//     try {

	//         $todos = profil_visit::orderBy('id', 'desc')->paginate(10);
	//         return response()->json([
	//             'code' => 200,
	//             'status' => true,
	//             'total' => $todos->total(),
	//             'last_page' => $todos->lastPage(),
	//             'data' => $todos->items(),
	//         ], 200);
	//     } catch (\Exception $e) {
	//         return response()->json([
	//             'code' => 409,
	//             'status' => false,
	//             'message' => 'failed get data',
	//             'error' => $e->getMessage()
	//         ], 409);
	//     }
	// }


	public function getOne(int $id): JsonResponse
	{
		$visit = DB::table('profil_visit')
			->select('profil_visit.*', 'store_info_distri.store_name', 'user_info.*')
			->join('store_info_distri', 'profil_visit.store_id', '=', 'store_info_distri.store_id')
			->join('user_info', 'profil_visit.user', '=', 'user_info.user_id')
			->where('id', $id)
			->first();

		if (!$visit) {
			return $this->clientErrorResponse(
				statusCode: 404,
				success: false,
				msg: "Visit data with id {$id} not found.",
			);
		}

		return $this->successResponse(
			statusCode: 200,
			success: true,
			msg: "Successfully fetch visit {$id} data.",
			resource: $visit,
		);
	}

	public function updateOne(Request $request, int $id)
	{
		$validator = Validator::make($request->all(), [
			'photo_visit' => 'nullable',
			'photo_visit_out' => 'nullable',
			'tanggal_visit' => 'nullable',
			'purchase_order_in' => 'nullable',
			'condit_owner' => 'nullable',
			'lat_in' => 'nullable',
			'long_in' => 'nullable',
			'lat_out' => 'nullable',
			'long_out' => 'nullable',
		]);

		if ($validator->fails()) {
			return $this->clientErrorResponse(
				statusCode: 422,
				success: false,
				msg: $validator->errors()->first(),
			);
		}

		$visit = ProfilVisit::where('id', $id)->firstOrFail();

		try {
			DB::beginTransaction();

			$visit->update([
				'photo_visit' => $request->data['photo_visit'],
				'photo_visit_out' => $request->data['photo_visit_out'],
				'tanggal_visit' => $request->data['tanggal_visit'],
				'purchase_order_in' => $request->data['purchase_order_in'],
				'condit_owner' => $request->data['condit_owner'],
				'lat_in' => $request->data['lat_in'],
				'long_in' => $request->data['long_in'],
				'lat_out' => $request->data['lat_out'],
				'long_out' => $request->data['long_out'],
				'approval' => $request->data['approval'],
			]);

			DB::commit();

			return $this->successResponse(
				statusCode: 200,
				success: true,
				msg: "Successfully update visit {$id} data.",
			);
		} catch (\Symfony\Component\HttpKernel\Exception\HttpException $e) {
			DB::rollBack();

			return $this->errorResponse(
				statusCode: $e->getStatusCode(),
				success: false,
				msg: $e->getMessage(),
			);
		} catch (\Error $e) {
			DB::rollBack();

			return $this->errorResponse(
				statusCode: 500,
				success: false,
				msg: $e->getMessage(),
			);
		} catch (\Exception $e) {
			DB::rollBack();

			return $this->errorResponse(
				statusCode: 500,
				success: false,
				msg: $e->getMessage(),
			);
		}
	}

	public function removeOne(int $id): JsonResponse
	{
		$visit = ProfilVisit::findOrFail($id);

		$visit->delete();

		return $this->successResponse(
			statusCode: 200,
			success: true,
			msg: "Successfully remove visit {$id} data.",
		);
	}
}
