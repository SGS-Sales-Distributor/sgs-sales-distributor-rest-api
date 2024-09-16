<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller; 
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ProfilNotvisit extends Controller
{
    public function saveOneData(Request $request): JsonResponse
    {
        return $this->profilNotvisitInterface->saveOneData($request);
    }
}
