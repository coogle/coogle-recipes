<?php

namespace App\Util;

class Text
{
    static public function fractionStringToFloat($str)
    {
        $str = trim($str);
        $str = preg_replace('!\s+!', ' ', $str);
        
        $int = 0;
        $float = 0;

        $parts = explode(' ', $str);
        
        if (count($parts) >= 1) {
            $int = $parts[0];
        }
        
        if (count($parts) >= 2) {
            $float_str = $parts[1];
            list($top, $bottom) = explode('/', $float_str);
            
            if($bottom > 0) {
                $float = $top / $bottom;
            } else {
                $float = $top;
            }
        }
        
        return $int + $float;
    }
}