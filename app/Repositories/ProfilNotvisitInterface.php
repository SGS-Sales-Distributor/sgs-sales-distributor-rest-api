<?php

namespace App\Repositories;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

interface profilNotvisitInterface{

    public function saveOneData(Request $request):JsonResponse;
}