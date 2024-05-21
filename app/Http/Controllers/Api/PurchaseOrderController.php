<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\OrderCustomerSales;
use App\Models\OrderCustomerSalesDetail;
use App\Models\PublicModel;
use App\Models\StoreInfoDistri;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Validator;

class PurchaseOrderController extends Controller
{
	public function getAll(Request $request): JsonResponse
	{
		$url =  URL::current();

		$orderModel = new OrderCustomerSales();

		$publicModel = new PublicModel();

		if (!isset($request->search)) {
			$pagination = $publicModel->paginateDataWithoutSearchQuery(
				$url,
				$request->limit,
				$request->offset,
			);

			$orders =  $orderModel->getAllData(
				$request->search,
				$pagination
			);

			$countData = $orders->count();
		} else {
			$pagination = $publicModel->paginateDataWithSearchQuery(
				$url,
				$request->limit,
				$request->offset,
				$request->search,
			);

			$orders = $orderModel->getAllData(
				$request->search,
				$pagination
			);

			$countData = $orders->count();
		}

		$countData = $orders->count();

		return $publicModel->successResponse(
			$orders,
			$countData,
			$pagination
		);
	}

	public function getAllOrder(): JsonResponse
	{
		$orders = DB::table('order_customer_sales')
		->select(
			'order_customer_sales.id',
			'order_customer_sales.no_order',
			'order_customer_sales.tgl_order',
			'order_customer_sales.cust_code',
			'order_customer_sales.order_sts',
			'order_customer_sales.created_at',
			'order_customer_sales.updated_at',
			'store_info_distri.store_name',
		)->join('store_info_distri', 'store_info_distri.store_code', '=', 'order_customer_sales.cust_code')
		->latest()
		->take(5)
		->get();

		return $this->successResponse(
			statusCode: 200,
			success: true,
			msg: "Successfully fetch orders data",
			resource: $orders,
		);
	}

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

	public function getOne(int $id): JsonResponse
	{
		$todo = OrderCustomerSales::with([
			'details',
			'status',
			'store.cabang'
		])
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
				'message' => 'Purchase Order data not found.',
			], 404);
		}
	}

	public function getAllDetail(): JsonResponse
	{
		$orderDetails = DB::table('order_customer_sales_detail')
		->select(
			'order_customer_sales_detail.id',
			'order_customer_sales_detail.orderId',
			'order_customer_sales_detail.lineNo',
			'order_customer_sales_detail.itemCode as item_code',
			'order_customer_sales_detail.qtyOrder as qty',
			'order_customer_sales_detail.created_at',
			'order_customer_sales_detail.updated_at',
			'order_customer_sales.no_order',
			'order_customer_sales.status_id',
			'brand.brand_id',
			'brand.brand_name as brand',
			'product_info_do.prod_number as nomor_produk',
			'product_info_do.prod_name as nama_produk',
		)
		->join('product_info_do', 'product_info_do.prod_number', '=', 'order_customer_sales_detail.itemCode')
		->join('brand', 'brand.brand_id', '=', 'product_info_do.brand_id')
		->join('order_customer_sales', 'order_customer_sales.id', '=', 'order_customer_sales_detail.orderId')
		->get();

		return $this->successResponse(
			statusCode: 200,
			success: true,
			msg: "Successfully fetch order details data.",
			resource: $orderDetails,
		);
	}

	public function getOneDetail(int $orderId): JsonResponse
	{
		// nama produk, itemCode, brand, qty
		$orderDetail = DB::table('order_customer_sales_detail')
			->select(
				'order_customer_sales_detail.id',
				'order_customer_sales_detail.orderId',
				'order_customer_sales_detail.lineNo',
				'order_customer_sales_detail.itemCode as item_code',
				'order_customer_sales_detail.qtyOrder as qty',
				'brand.brand_id',
				'brand.brand_name as brand',
				'product_info_do.prod_number as nomor_produk',
				'product_info_do.prod_name as nama_produk',
			)
			->join('product_info_do', 'product_info_do.prod_number', '=', 'order_customer_sales_detail.itemCode')
			->join('brand', 'brand.brand_id', '=', 'product_info_do.brand_id')
			->where('order_customer_sales_detail.orderId', '=', $orderId)
			->get();

		return response()
			->json([
				'data' => $orderDetail
			], 200);
	}

	public function updateOne(Request $request, int $id): JsonResponse
	{
		$validator = Validator::make($request->all(), [
			'data.no_order' => ['nullable', 'string'],
			'data.tgl_order' => ['required', 'date'],
			'data.tipe' => ['required', 'string'],
			'data.company' => ['required', 'integer'],
			'data.top' => ['required', 'integer'],
			'data.cust_code' => ['required', 'string'],
			'data.ship_code' => ['required', 'string'],
			'data.whs_code' => ['required', 'integer'],
			'data.whs_code_to' => ['required', 'integer'],
			'data.order_sts' => ['required', 'string'],
			'data.totOrderQty' => ['required', 'integer'],
			'data.totReleaseQty' => ['required', 'integer'],
			'data.keterangan' => ['required', 'string'],
			'data.llb_gabungan_reff' => ['required', 'string'],
			'data.llb_gabungan_sts' => ['required', 'string'],
			'data.store_id' => ['required', 'integer'],
			'data.status_id' => ['required', 'integer']
		]);

		if ($validator->fails()) {
			return $this->clientErrorResponse(
				statusCode: 422,
				success: false,
				msg: $validator->errors()->first(),
			);
		}

		$order = OrderCustomerSales::where('id', $id)->firstOrFail();

		$data = $request->data;

		try {
			$array = [
				'no_order' => $data['no_order'],
				'tgl_order' => $data['tgl_order'],
				'tipe' => $data['tipe'],
				'company' => $data['company'],
				'top' => $data['top'],
				'cust_code' => $data['cust_code'],
				'ship_code' => $data['ship_code'],
				'whs_code' => $data['whs_code'],
				'whs_code_to' => $data['whs_code_to'],
				'order_sts' => $data['order_sts'],
				'totOrderQty' => $data['totOrderQty'],
				'totReleaseQty' => $data['totReleaseQty'],
				'keterangan' => $data['keterangan'],
				'llb_gabungan_reff' => $data['llb_gabungan_reff'],
				'llb_gabungan_sts' => $data['llb_gabungan_sts'],
				'store_id' => $data['store_id'],
				'status_id' => $data['status_id'],
			];

			DB::beginTransaction();

			$order->update($array);

			DB::commit();

			//return successful response
			return response()->json([
				'status' => true,
				'message' => 'Form Purchase Order updated successfully.',
			], 200);
		} catch (\Exception $e) {
			//return error message
			return response()->json([
				'status' => false,
				'message' => $e->getMessage(),
			], 409);
		}
	}

	public function removeOne(Request $request, int $id)
	{
		$order = OrderCustomerSales::findOrFail($id);

		$order->delete();

		return response()->json([
			'status' => true,
			'message' => 'Form Purchaser Order deleted successfully.',
		], 200);
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
}
