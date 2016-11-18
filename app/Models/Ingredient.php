<?php

namespace App\Models;

class Ingredient extends \Eloquent
{
    protected $table = "ingredients";
    
    static public function findByString($str)
    {
        if(ctype_digit($str)) {
            return static::findOrFail($str);
        }
        
        return static::where('name', 'LIKE', "%$str%")->limit(1)->first();
    }
}