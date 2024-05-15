<?php

namespace App\Handlers;

class RandomStoreCode
{
    public function generateRandomCode(int $length): string
    {       
        $prefix = 'OS-';
        $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $code = $prefix;

        $charactersLength = strlen($characters);
        
        for ($i = 0; $i < $length - strlen($prefix); $i++) {
            $code .= $characters[random_int(0, $charactersLength - 1)];
        }

        return $code;
    }    
}
