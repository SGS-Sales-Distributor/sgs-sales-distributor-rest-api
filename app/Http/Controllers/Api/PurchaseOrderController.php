<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\OrderCustomerSales;
use App\Models\StoreInfoDistri;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class PurchaseOrderController extends Controller
{
	public function getAll(Request $request): JsonResponse
	{
		$searchByQuery = $request->query('q');

		$orders = OrderCustomerSales::withWhereHas('store', function ($query) use ($searchByQuery) {
			$query->where('store_name', 'LIKE', '%' . $searchByQuery . '%');
		})->with(['status', 'details', 'store.cabang'])
			->orderBy('no_order', 'asc')
			->paginate(50);

		return $this->successResponse(
			statusCode: 200,
			success: true,
			msg: "Successfully fetch orders data.",
			resource: $orders,
		);
	}


	// public function index()
	// {
	//     try {

	//         $todos = order_customer_sales::orderBy('modified_at', 'desc')->paginate(10);
	//         // $todos = program::with('programa')->get();
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

	public function storeOne(Request $request): JsonResponse
	{
		$validator = Validator::make($request->all(), [
			'no_order' => ['nullable', 'string'],
			'tgl_order' => ['required', 'date'],
			'tipe' => ['required', 'string'],
			'company' => ['required', 'integer'],
			'top' => ['required', 'integer'],
			'cust_code' => ['required', 'string'],
			'ship_code' => ['required', 'string'],
			'whs_code' => ['required', 'integer'],
			'whs_code_to' => ['required', 'integer'],
			'order_sts' => ['required', 'string'],
			'totOrderQty' => ['required', 'integer'],
			'totReleaseQty' => ['required', 'integer'],
			'keterangan' => ['required', 'string'],
			'llb_gabungan_reff' => ['required', 'string'],
			'llb_gabungan_sts' => ['required', 'string'],
			'store_id' => ['required', 'integer'],
			'status_id' => ['required', 'integer']
		]);

		if ($validator->fails()) {
			return $this->clientErrorResponse(
				statusCode: 422,
				success: false,
				msg: $validator->errors()->first(),
			);
		}

		$store = StoreInfoDistri::where('id', $request->store_id)->firstOrFail();

		try {
			$nomorPO = date('YmdHis') . $request->store_id;

			DB::beginTransaction();

			$order = OrderCustomerSales::create([
				'no_order' => $nomorPO,
				'tgl_order' => Carbon::createFromFormat('Y-m-d', $request->tgl_order),
				'tipe' => $request->tipe,
				'company' => $request->company,
				'top' => $request->top,
				'cust_code' => str_replace('-', '', $store->store_code),
				'ship_code' => $request->ship_code,
				'whs_code' => $request->whs_code,
				'whs_code_to' => $request->whs_code_to,
				'order_sts' => $request->order_sts,
				'totOrderQty' => $request->totOrderQty,
				'totReleaseQty' => $request->totReleaseQty,
				'keterangan' => $request->metodePembayaran,
				'llb_gabungan_reff' => $request->llb_gabungan_reff,
				'llb_gabungan_sts' => $request->llb_gabungan_sts,
				'uploaded_at' => Carbon::now(env('APP_TIMEZONE'))->format('Y-m-d H:i:s'),
				'uploaded_by' => "iduser",
				'store_id' => $request->store_id,
				'status_id' => $request->status_id,
			]);

			DB::commit();

			return $this->successResponse(
				statusCode: 201,
				success: true,
				msg: "Successfully create new order",
				resource: $order,
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

	public function show(int $id): JsonResponse
	{
		//$todo = $this->MUom->findOrFail($id);
		//die($id);
		$todo = OrderCustomerSales::with(['details', 'status', 'store.cabang'])
			->where('id', $id)
			->firstOrFail();

		if ($todo) {
			$sql = "
            SELECT order_customer_sales.*, store_info_distri.store_name FROM order_customer_sales
            INNER JOIN store_info_distri ON order_customer_sales.store_id = store_info_distri.store_id
            WHERE id = $id LIMIT 1";

			$data = DB::select($sql);

			return response()->json(['data' => $data[0]], 200);
		} else {
			return response()->json([
				'status' => false,
				'message' => $this->judul_halaman_notif . ' data not found.',
			], 404);
		}
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  \App\Models\order_customer_sales  $order_customer_sales
	 * @return \Illuminate\Http\Response
	 */
	public function edit(order_customer_sales $order_customer_sales)
	{
		//
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  \App\Models\order_customer_sales  $order_customer_sales
	 * @return \Illuminate\Http\Response
	 */
	public function update(Request $request, int $id)
	{
		//validate incoming request 
		//echo "<pre>";echo print_r($request);die();

		//$type_id = $request->typeid;
		$data = $this->validate($request, [
			'data.type' => 'required',
		]);

		try {
			$todo = order_customer_sales::findOrFail($id);
			$todo->fill($data['data']);
			$todo->save();

			// order_customer_sales::where('id', $id)->update(['updated_by' => $id, 'updated_at' => date('Y-m-d H:i:s')]);

			//return successful response
			return response()->json([
				'status' => true,
				'message' => $this->judul_halaman_notif . ' updated successfully.',
				'data' => $todo
			], 201);
		} catch (\Exception $e) {

			//return error message
			return response()->json([
				'status' => false,
				'message' => $this->judul_halaman_notif . ' failed update.',
			], 409);
		}
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  \App\Models\order_customer_sales  $order_customer_sales
	 * @return \Illuminate\Http\Response
	 */
	public function destroy(Request $request, int $id)
	{
		//echo "<pre>";echo print_r($request);die();
		// $id = $request->typeid;

		try {
			$todo = order_customer_sales::findOrFail($id);
			// order_customer_sales::where('id', $id)->update(['deleted_by' => $id]);
			$todo->delete();
			//untuk me-restore softdelete
			// $this->MUom->where('id', $id)->withTrashed()->restore();
			// order_customer_sales::withTrashed()->where('id', $id)->restore();

			//return successful response
			return response()->json([
				'status' => true,
				'message' => $this->judul_halaman_notif . ' deleted successfully.',
				'user' => $todo
			], 201);
		} catch (\Exception $e) {

			//return error message
			return response()->json([
				'status' => false,
				'message' => $this->judul_halaman_notif . ' failed delete.',
			], 409);
		}
	}

	public function getIDstore()
	{
		$type = (new store_info_distri())->getIDstore();
		return response()->json(
			['data' => $type],
			200
		);
	}

	public function getPurchaseOrder(Request $request)
	{


		try {


			$data = DB::table('order_customer_sales as t1')
				->join('store_info_distri as t2', 't1.store_id', '=', 't2.store_id')
				->select(
					't1.id as ID PO',
					't1.cust_code as CODE CUSTOMER',
					't1.order_sts as STATUS ORDER',
					't1.totOrderQty as QUANTITY',
					// 't1.store_id as ID OUTLET',
					't2.store_name as NAMA OUTLET',
				)
				->get();


			return response()->json([
				'status' => true,
				'data' => $data
			], 200);
		} catch (\Exception $e) {
			return response()->json([
				'status' => false,
				'message' => $e->getMessage()
			], 500);
		}
	}
}
