<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;

class OrderCustomerSalesController extends Controller
{
    public function showDetailOrder(Request $request, int $id): JsonResponse
    {
        return $this->OrderCustomerSalesInterface->showDetailOrder($request, $id);
    }
}
