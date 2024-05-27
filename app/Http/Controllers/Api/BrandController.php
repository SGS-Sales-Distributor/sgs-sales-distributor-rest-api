<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BrandController extends Controller
{
    public function getAll(Request $request): JsonResponse
    {
        return $this->brandInterface->getAllData($request);
    }

    public function getOne(string $id): JsonResponse
    {
        return $this->brandInterface->getOneData($id);
    }

    public function getAllProducts(Request $request, string $id): JsonResponse
    {
        return $this->brandInterface->getAllProductsData($request, $id);
    }

    public function getOneProduct(string $id, string $productNumber): JsonResponse
    {
        return $this->brandInterface->getOneProductData($id, $productNumber);
    }

    public function storeOne(Request $request): JsonResponse
    {
        return $this->brandInterface->storeOneData($request);
    }

    public function updateOne(Request $request, string $id): JsonResponse
    {
        return $this->brandInterface->updateOneData($request, $id);
    }

    public function removeOne(string $id): JsonResponse
    {
        return $this->brandInterface->removeOneData($id);
    }

    public function getAllGroups(Request $request): JsonResponse
    {
        return $this->brandInterface->getAllGroupsData($request);
    }

    public function getOneGroup(int $id): JsonResponse
    {
        return $this->brandInterface->getOneGroupData($id);
    }

    public function storeOneGroup(Request $request): JsonResponse
    {
        return $this->brandInterface->storeOneGroupData($request);
    }

    public function updateOneGroup(Request $request, int $id): JsonResponse
    {
        return $this->brandInterface->updateOneGroupData($request, $id);
    }

    public function removeOneGroup(int $id): JsonResponse
    {
        return $this->brandInterface->removeOneGroupData($id);
    }
}