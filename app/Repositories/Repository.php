<?php

namespace App\Repositories;

use App\Handlers\DateRangeFilter;
use App\Handlers\JwtAuthToken;
use App\Handlers\RandomDigitNumber;
use App\Handlers\RandomStoreCode;
use App\Traits\ApiResponse;

abstract class Repository
{
    use ApiResponse;

    public const DEFAULT_PAGINATE = 50;

    public const DEFAULT_CACHE_TTL = 5;

    protected RandomDigitNumber $randomDigitNumber;
    protected RandomStoreCode $randomStoreCode;
    protected DateRangeFilter $dateRangeFilter;
    protected JwtAuthToken $jwtAuthToken;

    public function __construct(
        RandomDigitNumber $randomDigitNumber,
        RandomStoreCode $randomStoreCode,
        DateRangeFilter $dateRangeFilter,
        JwtAuthToken $jwtAuthToken,
    )
    {
        $this->randomDigitNumber = $randomDigitNumber;
        $this->randomStoreCode = $randomStoreCode;
        $this->dateRangeFilter = $dateRangeFilter;
        $this->jwtAuthToken = $jwtAuthToken;
    }
}