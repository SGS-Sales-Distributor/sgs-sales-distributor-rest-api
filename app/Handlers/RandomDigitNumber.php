<?php

namespace App\Handlers;

use LogicException;

class RandomDigitNumber
{
    public function generateRandomNumber(
        int $initialLength = 9, 
        int $maxLength = 10
    ): string
    {
        if ($initialLength >= $maxLength) {
            throw new LogicException(
                message: "Lowest value should less than or could not same as max value."
            );
        }

        $min = pow(10, $initialLength);
        $max = pow(10,$maxLength) - 1;
        return (string) mt_rand($min, $max);
    }
}