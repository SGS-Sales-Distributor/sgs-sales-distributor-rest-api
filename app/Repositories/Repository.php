<?php

namespace App\Repositories;

use App\Handlers\DateRangeFilter;
use App\Handlers\RandomDigitNumber;
use App\Traits\ApiResponse;

abstract class Repository
{
    use ApiResponse;

    public const DEFAULT_PAGINATE = 100;

    public const DEFAULT_CACHE_TTL = 3;

    protected RandomDigitNumber $randomDigitNumber;
    protected DateRangeFilter $dateRangeFilter;

    public function __construct(
        RandomDigitNumber $randomDigitNumber,
        DateRangeFilter $dateRangeFilter,
    )
    {
        $this->randomDigitNumber = $randomDigitNumber;
        $this->dateRangeFilter = $dateRangeFilter;
    }
}