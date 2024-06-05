<?php

namespace App\Handlers;

use Carbon\Carbon;

class DateRangeFilter
{
    public function parseDate(string $queryParam)
    {
        $matches = [];

        $datePattern = preg_match('/date:(\d{4}-\d{2}-\d{2})/', $queryParam, $matches);

        if ($datePattern)
        {
            $date = Carbon::parse($matches[1]);
            
            return $date;
        }
    }

    public function parseDateRange(string $queryParam)
    {
        $matches = [];

        $rangeDatePattern = preg_match('/date:(\d{4}-\d{2}-\d{2}..?\d{4}-\d{2}-\d{2})/', $queryParam, $matches); 

        if ($rangeDatePattern)
        {
            $dates = explode('..', $matches[1]);
            
            $startDate = Carbon::parse($dates[0])->startOfDay();

            $endDate = Carbon::parse($dates[1])->endOfDay();

            return [$startDate, $endDate];
        }
    }

    public function parseYear(string $queryParam)
    {
        $matches = [];

        $yearPattern = preg_match('/date:(\d{4})/', $queryParam, $matches);

        if ($yearPattern)
        {
            $startCurrentYear = Carbon::createFromDate($matches[1], 1, 1);

            $endCurrentYear = Carbon::createFromDate($matches[1], 12, 31);

            return [$startCurrentYear, $endCurrentYear];
        }
    }

    public function parseYearRange(string $queryParam)
    {
        $matches = [];

        $yearRangePattern = preg_match('/date:(\d{4}..?\d{4})/', $queryParam, $matches);
        
        if ($yearRangePattern)
        {
            $years = explode('..', $matches[1]);

            $startYear = Carbon::createFromDate($years[0], 1, 1);

            $endYear = Carbon::createFromDate($years[1], 12, 31);

            return [$startYear, $endYear];
        }
    }
}