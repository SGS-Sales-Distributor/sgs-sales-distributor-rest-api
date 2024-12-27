<?php

namespace App\Repositories;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use App\Models\OrderCustomerSales;
use App\Models\OrderCustomerSalesDetail;
use Illuminate\Support\Facades\DB;

class OrderCustomerSalesRepository extends Repository implements OrderCustomerSalesInterface
{
    public function getAllData(Request $request): JsonResponse
    {
        $searchByQuery = $request->query('q');

        $orderCustomersCache = Cache::remember('');
    }

    public function getOneData(int $id): JsonResponse
    {

    }


    public function showDetailOrder(Request $request, int $id): JsonResponse
    {
        // $order = OrderCustomerSales::find($id);
        $order = OrderCustomerSales::join('store_info_distri', 'store_info_distri.store_id', '=', 'order_customer_sales.store_id')
            ->select('order_customer_sales.*', 'store_info_distri.store_phone')->find($id);
        $orderDetail = DB::table('order_customer_sales_detail')
            ->select('product_info_do.*', 'order_customer_sales_detail.*', 'product_info_do.prod_number as prodNumber', 'product_info_do.prod_name as prodName', 'product_info_do.prod_unit_price as prodPrice', 'order_customer_sales_detail.qtyOrder as qty')
            ->join('product_info_do', 'product_info_do.prod_number', '=', 'order_customer_sales_detail.itemCodeCust')
            ->where('order_customer_sales_detail.orderId', $id)
            ->get();
        // $log = DB::getQueryLog();
        // dd($log);
        $hasil = [
            "order" => $order,
            "orderDetail" => $orderDetail,
        ];


        // if (!$data) {
        //     return response()->json(['message' => 'Order not found'], 404);
        // }

        // return response()->json($data);
        // // DB::enableQueryLog();
        // // $orderDetail = OrderCustomerSalesDetail::where("orderId",$id)->first();
        // $data = OrderCustomerSales::with([
        //         'details',
        //         // 'productDetails'
        //         ]
        //         )
        //         // ->join('product_info_do', 'order_customer_sales_detail.itemCode', '=', 'product_info_do.prod_number')
        //         ->where('id','=',$id)
        //         // ->where('product_info_do.prod_number', '=', 'order_customer_sales_detail.itemCodeCust')
        //         ->get();
        // $log = DB::getQueryLog();
        // dd($log);

        // if (count($data) == 0) {
        //     return $this->clientErrorResponse(
        //         statusCode: 404,
        //         success: false,
        //         msg: "Detail Order id : {$id} not found.",
        //     );
        // }

        return $this->successResponse(
            statusCode: 200,
            success: true,
            msg: "Successfully Show Detail Order id: {$id}.",
            resource: $hasil,
        );
    }
}