<?php

namespace App\Repositories;

use App\Traits\ApiResponse;

abstract class Repository
{
    use ApiResponse;

    public const DEFAULT_PAGINATE = 100;

    public const DEFAULT_CACHE_TTL = 3;
}