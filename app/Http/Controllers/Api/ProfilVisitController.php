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

		DB::enableQueryLog();
		// $visits = ProfilVisit::with(['user', 'store', 'masterPlanDtl'])
		$visits = DB::table('master_call_plan_detail')
			->select([
				'profil_visit.id as id',
				'store_info_distri.store_id',
				'store_info_distri.store_name as nama_toko',
				'store_info_distri.store_alias as alias_toko',
				'store_info_distri.store_address as alamat_toko',
				'store_info_distri.store_phone as nomor_telepon_toko',
				'store_info_distri.store_fax as nomor_fax_toko',
				'store_info_distri.store_type_id',
				'store_info_distri.subcabang_id',
				'store_info_distri.active as status_toko',
				'store_info_distri.store_code as kode_toko',
				'profil_visit.id as visit_id',
				'profil_visit.user as nama_salesman',
				'profil_visit.tanggal_visit as tanggal_visit',
				'profil_visit.time_in as waktu_masuk',
				'profil_visit.time_out as waktu_keluar',
				'profil_visit.photo_visit as photo_visit',
				'profil_visit.photo_visit_out as photo_visit_out',
				'profil_visit.ket as keterangan',
				'profil_visit.approval as approval',
				'profil_visit.ket as keterangan',
				'master_call_plan_detail.date as tanggal_plan',
				'user_info.fullname as userSalesman',
			])
			->join('store_info_distri', 'store_info_distri.store_id', '=', 'master_call_plan_detail.store_id')
			->join('master_call_plan', 'master_call_plan.id', '=', 'master_call_plan_detail.call_plan_id')
			->join('user_info', 'user_info.user_id', '=', 'master_call_plan.user_id')
			->leftJoin('profil_visit', function ($leftJoin) {
				$leftJoin->on('profil_visit.user', '=', 'master_call_plan.user_id')
					->on('profil_visit.tanggal_visit', '=', 'master_call_plan_detail.date')
					->on('profil_visit.store_id', '=', 'master_call_plan_detail.store_id');
			})
			->when($searchByQuery, function (Builder $query) use ($searchByQuery) {
				$query->where('user', 'LIKE', '%' . $searchByQuery . '%');
			})->orderBy('master_call_plan_detail.date', 'asc')

			->paginate(50);
		$log = DB::getQueryLog();
		dd($log);

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

	public function getVisitUser(int $userId, Request $request): JsonResponse
	{
		// $searchByQuery = $request->query('q');
		// DB::enableQueryLog();
		$visit = DB::table('master_call_plan_detail')
			->select([
				'profil_visit.id as id',
				'store_info_distri.store_id',
				'store_info_distri.store_name as nama_toko',
				'store_info_distri.store_alias as alias_toko',
				'store_info_distri.store_address as alamat_toko',
				'store_info_distri.store_phone as nomor_telepon_toko',
				'store_info_distri.store_fax as nomor_fax_toko',
				'store_info_distri.store_type_id',
				'store_info_distri.subcabang_id',
				'store_info_distri.active as status_toko',
				'store_info_distri.store_code as kode_toko',
				'profil_visit.id as visit_id',
				'profil_visit.user as nama_salesman',
				'profil_visit.tanggal_visit as tanggal_visit',
				'profil_visit.time_in as waktu_masuk',
				'profil_visit.time_out as waktu_keluar',
				'profil_visit.photo_visit as photo_visit',
				'profil_visit.photo_visit_out as photo_visit_out',
				'profil_visit.ket as keterangan',
				'profil_visit.approval as approval',
				'profil_visit.ket as keterangan',
				'master_call_plan_detail.date as tanggal_plan',
				'user_info.fullname as userSalesman',
			])
			->join('store_info_distri', 'store_info_distri.store_id', '=', 'master_call_plan_detail.store_id')
			->join('master_call_plan', 'master_call_plan.id', '=', 'master_call_plan_detail.call_plan_id')
			->join('user_info', 'user_info.user_id', '=', 'master_call_plan.user_id')
			->leftJoin('profil_visit', function ($leftJoin) {
				$leftJoin->on('profil_visit.user', '=', 'master_call_plan.user_id')
					->on('profil_visit.tanggal_visit', '=', 'master_call_plan_detail.date')
					->on('profil_visit.store_id', '=', 'master_call_plan_detail.store_id');
			})
			// ->when($searchByQuery, function (Builder $query) use ($searchByQuery,$userId) {
				// $query	->where('nama_toko', 'LIKE', '%' . $searchByQuery . '%')
						->where('master_call_plan.user_id', DB::raw("'".$userId."'"))
						->where('user_info.user_id',  DB::raw("'".$userId."'"))
			->orderBy('master_call_plan_detail.date', 'desc')
			->get();

		// $log = DB::getQueryLog();
		// dd($log);

		if (!$visit) {
			return $this->clientErrorResponse(
				statusCode: 404,
				success: false,
				msg: "Visit data UserId : {$userId} not found.",
			);
		}

		return $this->successResponse(
			statusCode: 200,
			success: true,
			msg: "Successfully fetch Visit UserId : {$userId} data.",
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
				// 'photo_visit' => $request->photo_visit,
				// 'photo_visit_out' => $request->photo_visit_out,
				// 'tanggal_visit' => $request->tanggal_visit,
				// 'purchase_order_in' => $request->purchase_order_in,
				// 'condit_owner' => $request->condit_owner,
				// 'lat_in' => $request->lat_in,
				// 'long_in' => $request->long_in,
				// 'lat_out' => $request->lat_out,
				// 'long_out' => $request->long_out,
				'approval' => 1,
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
