<?php

namespace App\Repositories;

use App\Models\OrderCustomerSales;
use App\Models\OrderCustomerSalesDetail;
use App\Models\StoreInfoDistri;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Query\Builder;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class OrderCustomerRepository extends Repository implements OrderCustomerInterface
{
    public function getAllData(Request $request): JsonResponse
    {
        $currentMonth = now(env('APP_TIMEZONE'))->month;
        $currentYear = now(env('APP_TIMEZONE'))->year;

        $searchByQuery = $request->query('q');

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
            ->when($searchByQuery, function (Builder $query) use ($searchByQuery) {
                $query->where('order_customer_sales.no_order', 'LIKE', '%' . $searchByQuery . '%')
                    ->orWhere('store_info_distri.store_name', 'LIKE', '%' . $searchByQuery . '%');
            })
            ->whereMonth('order_customer_sales.created_at', '=', $currentMonth)
            ->whereYear('order_customer_sales.created_at', '=', $currentYear)
            ->latest()
            ->get();

        if ($searchByQuery === 'latest') {
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
                ->whereMonth('order_customer_sales.created_at', '=', $currentMonth)
                ->whereYear('order_customer_sales.created_at', '=', $currentYear)
                ->latest()
                ->get();
        }

        if ($searchByQuery === 'order-number-asc') {
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
                ->whereMonth('order_customer_sales.created_at', '=', $currentMonth)
                ->whereYear('order_customer_sales.created_at', '=', $currentYear)
                ->orderBy('order_customer_sales.no_order', 'asc')
                ->get();
        }

        if ($searchByQuery === 'order-number-desc') {
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
                ->whereMonth('order_customer_sales.created_at', '=', $currentMonth)
                ->whereYear('order_customer_sales.created_at', '=', $currentYear)
                ->orderBy('order_customer_sales.no_order', 'desc')
                ->get();
        }

        if ($searchByQuery === 'store-name-asc') {
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
                ->whereMonth('order_customer_sales.created_at', '=', $currentMonth)
                ->whereYear('order_customer_sales.created_at', '=', $currentYear)
                ->orderBy('store_info_distri.store_name', 'asc')
                ->get();
        }

        if ($searchByQuery === 'store-name-desc') {
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
                ->whereMonth('order_customer_sales.created_at', '=', $currentMonth)
                ->whereYear('order_customer_sales.created_at', '=', $currentYear)
                ->orderBy('store_info_distri.store_name', 'desc')
                ->get();
        }

        return $this->successResponse(
            statusCode: 200,
            success: true,
            msg: "Successfully fetch orders data",
            resource: $orders,
        );
    }

    public function getOneData(int $id): JsonResponse
    {
        $order = DB::table('order_customer_sales')
            ->select(
                'order_customer_sales.id',
                'order_customer_sales.no_order',
                'order_customer_sales.tgl_order',
                'order_customer_sales.cust_code',
                'order_customer_sales.order_sts',
                'order_customer_sales.created_at',
                'order_customer_sales.updated_at',
                'store_info_distri.store_name',
            )
            ->join('store_info_distri', 'store_info_distri.store_code', '=', 'order_customer_sales.cust_code')
            ->where('order_customer_sales.id')
            ->first();

        return $this->successResponse(
            statusCode: 200,
            success: true,
            msg: "Successfully fetch orders {$id} data",
            resource: $order,
        );
    }

    public function getAllDetailsData(Request $request, int $id): JsonResponse
    {
        $searchByQuery = $request->query('q');

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
            )->when($searchByQuery, function (Builder $query) use ($searchByQuery) {
                $query->where('itemCodeCust', 'LIKE', '%' . $searchByQuery . '%')
                    ->orWhere('itemCode', 'LIKE', '%' . $searchByQuery . '%');
            })
            ->join('product_info_do', 'product_info_do.prod_number', '=', 'order_customer_sales_detail.itemCode')
            ->join('brand', 'brand.brand_id', '=', 'product_info_do.brand_id')
            ->join('order_customer_sales', 'order_customer_sales.id', '=', 'order_customer_sales_detail.orderId')
            ->where('order_customer_sales_detail.orderId', $id)
            ->get();

        return $this->successResponse(
            statusCode: 200,
            success: true,
            msg: "Successfully fetch order {$id} details data",
            resource: $orderDetails,
        );
    }

    public function getOneDetailData(int $id, int $detailId): JsonResponse
    {
        $orderDetail = OrderCustomerSalesDetail::withWhereHas('order', function ($query) use ($id) {
            $query->where('id', $id);
        })->where('id', $detailId)->firstOrFail();

        return $this->successResponse(
            statusCode: 200,
            success: true,
            msg: "Successfully fetch orders {$id} details {$detailId} data",
            resource: $orderDetail,
        );
    }

    public function storeOneData(Request $request): JsonResponse
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

    public function updateOneData(Request $request, int $id): JsonResponse
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

        $order = OrderCustomerSales::where('id', $id)->firstOrFail();

        try {
            DB::beginTransaction();

            $order->update([
                'no_order' => $request->no_order,
                'tgl_order' => Carbon::createFromFormat('Y-m-d', $request->tgl_order),
                'tipe' => $request->tipe,
                'company' => $request->company,
                'top' => $request->top,
                'cust_code' => $request->cust_code,
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
                statusCode: 200,
                success: true,
                msg: "Successfully update recent order {$id}"
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

    public function removeOneData(int $id): JsonResponse
    {
        $order = OrderCustomerSales::where('id', $id)->firstOrFail();

        $order->delete();

        return $this->successResponse(
            statusCode: 200,
            success: true,
            msg: "Successfully remove recent order {$id}"
        );
    }
}
