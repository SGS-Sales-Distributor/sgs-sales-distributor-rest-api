<?php

namespace App\Repositories;

use App\Models\ProductInfoDo;
use App\Models\PublicModel;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Validator;

class ProductRepository extends Repository implements ProductInterface
{
    public function getAllData(Request $request): JsonResponse
    {
        $searchByQuery = $request->query('q');

        $products = ProductInfoDo::with([
            'status',
            'brand',
            'type',
            'productInfoLmts',
            'dataReturDetails',
        ])
            ->when($searchByQuery, function (Builder $query) use ($searchByQuery) {
                $query->where('prod_name', 'LIKE', '%' . $searchByQuery . '%')
                    ->orWhere('prod_number', 'LIKE', '%' . $searchByQuery . '%');
            })
            ->orderBy('prod_number', 'asc')
            ->paginate($this::DEFAULT_PAGINATE);

        return $this->successResponse(
            statusCode: 200,
            success: true,
            msg: "Successfully fetch products.",
            resource: $products,
        );
    }

    public function getOneData(string $productNumber): JsonResponse
    {
        $productCache = Cache::remember(
            "products:{$productNumber}",
            $this::DEFAULT_CACHE_TTL,
            function () use ($productNumber) {
                return ProductInfoDo::with([
                    'status',
                    'brand',
                    'type',
                    'productInfoLmts',
                    'dataReturDetails',
                ])->where('prod_number', $productNumber)
                    ->firstOrFail();
            }
        );

        return $this->successResponse(
            statusCode: 200,
            success: true,
            msg: "Successfully fetch product {$productNumber}.",
            resource: $productCache,
        );
    }

    public function storeOneData(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'product_number' => ['required', 'string', 'max:100', 'unique:product_info_do,prod_number'],
            'product_barcode_number' => ['required', 'string', 'max:30', 'unique:product_info_do,prod_barcode_number'],
            'product_universal_number' => ['required', 'string', 'max:30', 'unique:product_info_do,prod_universal_number'],
            'product_name' => ['required', 'string', 'max:100'],
            'product_base_price' => ['required', 'max:20', 'string'],
            'product_unit_price' => ['required', 'max:20', 'string'],
            'product_promo_price' => ['required', 'max:20', 'string'],
            'product_special_offer' => ['required', 'max:20', 'string'],
            'product_special_offer_unit' => ['required', 'max:20', 'string'],
            'brand_id' => ['required', 'string', 'max:10'],
            'category_id' => ['required', 'string', 'max:10'],
            'category_sub_id' => ['required', 'string', 'max_digits:10'],
            'product_type_id' => ['nullable', 'integer'],
            'supplier_id' => ['nullable', 'string', 'max:10'],
            'product_status_id' => ['nullable', 'integer'],
            'status_aktif' => ['required'],
            'created_by' => ['nullable', 'string', 'max:255'],
            'updated_by' => ['nullable', 'string', 'max:255'],
        ]);

        if ($validator->fails()) {
            return $this->clientErrorResponse(
                statusCode: 422,
                success: false,
                msg: $validator->errors()->first(),
                resource: $validator->errors()->all(),
            );
        }

        $brand = $request->input('brand');

        $typeProduct = $request->input('typeProduct');

        try {
            DB::beginTransaction();

            $product = ProductInfoDo::create([
                'prod_number' => $request->product_number,
                'prod_barcode_number' => $request->product_barcode_number,
                'prod_universal_number' => $request->product_universal_number,
                'prod_name' => $request->product_name,
                'prod_base_price' => $request->product_base_price,
                'prod_unit_price' => $request->product_unit_price,
                'prod_promo_price' => $request->product_promo_price,
                'prod_special_offer' => $request->product_special_offer,
                'prod_special_offer_unit' => $request->product_special_offer_unit,
                'brand_id' => $brand,
                'category_id' => $request->category_id,
                'category_sub_id' => $request->category_sub_id,
                'prod_type_id' => $typeProduct,
                'supplier_id' => $request->supplier_id,
                'prod_status_id' => $request->product_status_id,
                'status_aktif' => $request->status_aktif,
                'created_by' => $request->created_by,
                'updated_by' => $request->updated_by,
            ]);

            DB::commit();

            return $this->successResponse(
                statusCode: 201,
                success: true,
                msg: "Successfully create new products",
                resource: $product,
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

    public function updateOneData(Request $request, string $productNumber): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'product_number' => ['required', 'string', 'max:100', 'unique:product_info_do,prod_number'],
            'product_barcode_number' => ['required', 'string', 'max:30', 'unique:product_info_do,prod_barcode_number'],
            'product_universal_number' => ['required', 'string', 'max:30', 'unique:product_info_do,prod_universal_number'],
            'product_name' => ['required', 'string', 'max:100'],
            'product_base_price' => ['required', 'max:20', 'string'],
            'product_unit_price' => ['required', 'max:20', 'string'],
            'product_promo_price' => ['required', 'max:20', 'string'],
            'product_special_offer' => ['required', 'max:20', 'string'],
            'product_special_offer_unit' => ['required', 'max:20', 'string'],
            'brand_id' => ['required', 'string', 'max:10'],
            'category_id' => ['required', 'string', 'max:10'],
            'category_sub_id' => ['required', 'string', 'max_digits:10'],
            'product_type_id' => ['nullable', 'integer'],
            'supplier_id' => ['nullable', 'string', 'max:10'],
            'product_status_id' => ['nullable', 'integer'],
            'status_aktif' => ['required'],
            'updated_by' => ['nullable', 'string', 'max:255'],
        ]);

        if ($validator->fails()) {
            return $this->clientErrorResponse(
                statusCode: 422,
                success: false,
                msg: $validator->errors()->first(),
                resource: $validator->errors()->all(),
            );
        }

        $brand = $request->input('brand');

        $typeProduct = $request->input('typeProduct');

        $product = ProductInfoDo::where('prod_number', $productNumber)->firstOrFail();

        try {
            DB::beginTransaction();

            $product->update([
                'prod_number' => $request->product_number,
                'prod_barcode_number' => $request->product_barcode_number,
                'prod_universal_number' => $request->product_universal_number,
                'prod_name' => $request->product_name,
                'prod_base_price' => $request->product_base_price,
                'prod_unit_price' => $request->product_unit_price,
                'prod_promo_price' => $request->product_promo_price,
                'prod_special_offer' => $request->product_special_offer,
                'prod_special_offer_unit' => $request->product_special_offer_unit,
                'brand_id' => $brand,
                'category_id' => $request->category_id,
                'category_sub_id' => $request->category_sub_id,
                'prod_type_id' => $typeProduct,
                'supplier_id' => $request->supplier_id,
                'prod_status_id' => $request->product_status_id,
                'status_aktif' => $request->status_aktif,
                'updated_by' => $request->updated_by,
            ]);

            DB::commit();

            return $this->successResponse(
                statusCode: 200,
                success: true,
                msg: "Successfully update recent product {$productNumber}",
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

    public function removeOneData(string $productNumber): JsonResponse
    {
        $product = ProductInfoDo::where('prod_number', $productNumber)->firstOrFail();

        $product->delete();

        return $this->successResponse(
            statusCode: 200,
            success: true,
            msg: "Successfully rmeove recent product {$productNumber}",
        );
    }

    public function getAllBasic(): JsonResponse
    {
        try {
            $product = DB::table('product_info_do')->select(
                'prod_number as MTG CODE',
                'prod_name as PRODUCT NAME',
                'prod_base_price as PRODUCT BASE PRICE',
                'prod_special_offer as DISKON REGULAR',
                'brand_id as BRAND ID',
            )->get();

            return response()->json([
                'status' => true,
                'data' => $product
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function getAllBasicWithPaging(Request $request): JsonResponse
    {
        $url =  URL::current();

        $productModel = new ProductInfoDo();

        $publicModel = new PublicModel();

        if (!isset($request->search)) {
            $pagination = $publicModel->paginateDataWithoutSearchQuery(
                $url,
                $request->limit,
                $request->offset,
            );

            $products =  $productModel->getAllData(
                $request->search,
                $pagination
            );

            $countData = $products->count();
        } else {
            $pagination = $publicModel->paginateDataWithSearchQuery(
                $url,
                $request->limit,
                $request->offset,
                $request->search,
            );

            $products = $productModel->getAllData(
                $request->search,
                $pagination
            );

            $countData = $products->count();
        }

        return $publicModel->successResponse(
            $products,
            $countData,
            $pagination
        );
    }

    public function getOneBasic(string $productNumber): JsonResponse
    {
        $product = ProductInfoDo::with([
            'status',
            'brand',
            'type',
            'productInfoLmts',
            'orderDetails',
            'dataReturDetails',
        ])->where('prod_number', $productNumber)
        ->firstOrFail();
        
        return response()->json([
            'success' => true,
            'message' => "Successfully fetch product {$productNumber}",
            'data' => $product,
        ], 200);
    }

    public function storeBasicData(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'data.prod_base_price' => 'required',
            'data.prod_unit_price' => 'required',
            'data.prod_special_offer' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(
                $validator->errors(),
                422
            );
        }

        $data = $request->data;

        try {
            DB::beginTransaction();

            $product = ProductInfoDo::create($data);

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Form Master Product Created Successfully',
                'data' => $product,
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ], 403);
        }
    }

    public function updateBasicData(Request $request, string $productNumber): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'data.prod_base_price' => 'required',
            'data.prod_unit_price' => 'required',
            'data.prod_special_offer' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(
                $validator->errors(),
                422
            );
        }

        $data = $request->data;

        $product = ProductInfoDo::where('prod_number', $productNumber)->firstOrFail();

        try {
            $array = [
                'prod_base_price' => $data['prod_base_price'],
                'prod_unit_price' => $data['prod_unit_price'],
                'prod_special_offer' => $data['prod_special_offer']
            ];

            DB::beginTransaction();

            $product->update($array);

            DB::commit();

            //return successful response
            return response()->json([
                'status' => true,
                'message' => 'Form Master Product updated successfully.',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ], 409);
        }
    }

    public function removeBasicData(string $productNumber): JsonResponse
    {
        $product = ProductInfoDo::where('prod_number', $productNumber);

        $product->delete();

        return response()->json([
            'status' => true,
            'message' => 'Form Master Product deleted successfully.',
        ], 200);
    }
}
