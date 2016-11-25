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
        
        return static::where(\DB::raw('LOWER(name)'), '=', strtolower($str))->limit(1)->first();
    }
    
    static public function searchByString($str)
    {
        return static::where(\DB::raw('LOWER(name)'), 'LIKE', "%" . strtolower($str) . "%")
                     ->limit(20)
                     ->get();
    }
}