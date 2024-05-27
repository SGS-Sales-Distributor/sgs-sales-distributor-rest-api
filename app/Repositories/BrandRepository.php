<?php

namespace App\Repositories;

use App\Models\Brand;
use App\Models\BrandGroup;
use App\Models\ProductInfoDo;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class BrandRepository extends Repository implements BrandInterface
{
    public function getAllData(Request $request): JsonResponse
    {
        $searchByQuery = $request->query('q');

        $brandsCache = Cache::remember(
            'brandsCache',
            $this::DEFAULT_CACHE_TTL,
            function () use ($searchByQuery) {
                return Brand::with('brandGroup', 'products')
                    ->when($searchByQuery, function (Builder $query) use ($searchByQuery) {
                        $query->where('brand_name', 'LIKE', '%' . $searchByQuery . '%');
                    })
                    ->where('status', 1)
                    ->orderBy('brand_id', 'asc')
                    ->paginate($this::DEFAULT_PAGINATE);
            }
        );

        return $this->successResponse(
            statusCode: 200,
            success: true,
            msg: "Successfully fetch brands.",
            resource: $brandsCache,
        );
    }

    public function getOneData(string $id): JsonResponse
    {
        $brandCache = Cache::remember(
            "brand:{$id}",
            function () use ($id) {
                return Brand::with('brandGroup', 'products')
                    ->where('brand_id', $id)
                    ->firstOrFail();
            }
        );

        return $this->successResponse(
            statusCode: 200,
            success: true,
            msg: "Successfully fetch brand {$id}.",
            resource: $brandCache,
        );
    }

    public function getAllProductsData(Request $request, string $id): JsonResponse
    {
        $searchByQuery = $request->query('q');

        $products = ProductInfoDo::whereHas('brand', function (Builder $query) use ($id) {
            $query->where('brand_id', $id);
        })->with(['status'])
            ->when($searchByQuery, function (Builder $query) use ($searchByQuery) {
                $query->where('prod_number', 'LIKE', '%' . $searchByQuery . '%')
                    ->orWhere('prod_name', 'LIKE', '%' . $searchByQuery . '%');
            })->orderBy('prod_name', 'asc')
            ->get();

        return $this->successResponse(
            statusCode: 200,
            success: true,
            msg: "Successfully fetch products data based on brand {$id}.",
            resource: $products,
        );
    }

    public function getOneProductData(string $id, string $productNumber): JsonResponse
    {
        $product = ProductInfoDo::whereHas('brand', function (Builder $query) use ($id) {
            $query->where('brand_id', $id);
        })->where('prod_number', $productNumber)
            ->firstOrFail();

        return $this->successResponse(
            statusCode: 200,
            success: true,
            msg: "Successfully fetch product {$productNumber} based on brand {$id}.",
            resource: $product,
        );
    }

    public function storeOneData(Request $request): JsonResponse
    {
        $validator = Validator::make(
            $request->all(),
            [
                'brand_id' => ['required', 'string'],
                'brand_name' => ['required', 'string', 'max:255'],
                'status' => ['required', 'boolean'],
                'brand_group_id' => ['required', 'string'],
            ],
            [
                'required' => ':attribute is required!',
                'unique' => ':attribute is unique field!',
                'min' => ':attribute should be :min in characters',
                'max' => ':attribute could not more than :max characters',
                'confirmed' => ':attribute confirmation does not match!',
            ]
        );

        if ($validator->fails()) {
            return $this->clientErrorResponse(
                statusCode: 422,
                success: false,
                msg: $validator->errors()->first(),
            );
        }

        try {
            DB::beginTransaction();

            $brand = Brand::create([
                'brand_id' => $request->brand_id,
                'brand_name' => $request->brand_name,
                'status' => $request->status,
                'brand_group_id' => $request->brand_group_id,
            ]);

            DB::commit();

            return $this->successResponse(
                statusCode: 201,
                success: true,
                msg: "Successfully create new brand data",
                resource: $brand
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

    public function updateOneData(Request $request, string $id): JsonResponse
    {
        $validator = Validator::make(
            $request->all(),
            [
                'brand_id' => ['required', 'string'],
                'brand_name' => ['required', 'string', 'max:255'],
                'status' => ['required', 'boolean'],
                'brand_group_id' => ['required', 'string'],
            ],
            [
                'required' => ':attribute is required!',
                'unique' => ':attribute is unique field!',
                'min' => ':attribute should be :min in characters',
                'max' => ':attribute could not more than :max characters',
                'confirmed' => ':attribute confirmation does not match!',
            ]
        );

        if ($validator->fails()) {
            return $this->clientErrorResponse(
                statusCode: 422,
                success: false,
                msg: $validator->errors()->first(),
            );
        }

        $brand = Brand::where('brand_id', $id)->firstOrFail();

        try {
            DB::beginTransaction();

            $brand->update([
                'brand_id' => $request->brand_id,
                'brand_name' => $request->brand_name,
                'status' => $request->status,
                'brand_group_id' => $request->brand_group_id,
            ]);

            DB::commit();

            return $this->successResponse(
                statusCode: 200,
                success: true,
                msg: "Successfully update current brand {$id} data",
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

    public function removeOneData(string $id): JsonResponse
    {
        $brand = Brand::where('brand_id', $id)->firstOrFail();

        $brand->delete();

        return $this->successResponse(
            statusCode: 200,
            success: true,
            msg: "Successfully remove current brand {$id} data",
        );
    }

    public function getAllGroupsData(Request $request): JsonResponse
    {
        $searchByQuery = $request->query('q');

        $brandGroups = BrandGroup::with('brands')
            ->when($searchByQuery, function (Builder $query) use ($searchByQuery) {
                $query->where('brand_group_name', 'LIKE', '%' . $searchByQuery . '%');
            })->orderBy('brand_group_name', 'asc')
            ->paginate($this::DEFAULT_PAGINATE);

        return $this->successResponse(
            statusCode: 200,
            success: true,
            msg: "Successfully fetch brand groups.",
            resource: $brandGroups,
        );
    }

    public function getOneGroupData(int $id): JsonResponse
    {
        $brandGroup = BrandGroup::with('brands')->where('id', $id)->firstOrFail();

        return $this->successResponse(
            statusCode: 200,
            success: true,
            msg: "Successfully fetch brand groups {$id}.",
            resource: $brandGroup,
        );
    }

    public function storeOneGroupData(Request $request): JsonResponse
    {
        $validator = Validator::make(
            $request->all(),
            [
                'brand_group_id' => ['required', 'string'],
                'brand_group_name' => ['required', 'string', 'max:255'],
            ],
            [
                'required' => ':attribute is required!',
                'unique' => ':attribute is unique field!',
                'min' => ':attribute should be :min in characters',
                'max' => ':attribute could not more than :max characters',
                'confirmed' => ':attribute confirmation does not match!',
            ]
        );

        if ($validator->fails()) {
            return $this->clientErrorResponse(
                statusCode: 422,
                success: false,
                msg: $validator->errors()->first(),
            );
        }

        try {
            DB::beginTransaction();

            $brandGroup = BrandGroup::create([
                'brand_group_id' => $request->brand_group_id,
                'brand_group_name' => $request->brand_group_name,
            ]);

            DB::commit();

            return $this->successResponse(
                statusCode: 201,
                success: true,
                msg: "Successfully create new brand group data",
                resource: $brandGroup
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

    public function updateOneGroupData(Request $request, int $id): JsonResponse
    {
        $validator = Validator::make(
            $request->all(),
            [
                'brand_group_id' => ['required', 'string'],
                'brand_group_name' => ['required', 'string', 'max:255'],
            ],
            [
                'required' => ':attribute is required!',
                'unique' => ':attribute is unique field!',
                'min' => ':attribute should be :min in characters',
                'max' => ':attribute could not more than :max characters',
                'confirmed' => ':attribute confirmation does not match!',
            ]
        );

        if ($validator->fails()) {
            return $this->clientErrorResponse(
                statusCode: 422,
                success: false,
                msg: $validator->errors()->first(),
            );
        }

        $brandGroup = BrandGroup::where('id', $id)->firstOrFail();

        try {
            DB::beginTransaction();

            $brandGroup->update([
                'brand_group_id' => $request->brand_group_id,
                'brand_group_name' => $request->brand_group_name,
            ]);

            DB::commit();

            return $this->successResponse(
                statusCode: 201,
                success: true,
                msg: "Successfully update current brand group {$id}",
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

    public function removeOneGroupData(int $id): JsonResponse
    {
        $brandGroup = BrandGroup::where('id', $id)->firstOrFail();

        $brandGroup->delete();

        return $this->successResponse(
            statusCode: 200,
            success: true,
            msg: "Successfully remove current brand group {$id} data",
        );
    }
}
