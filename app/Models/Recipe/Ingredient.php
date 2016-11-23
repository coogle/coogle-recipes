<?php

namespace App\Models\Recipe;

class Ingredient extends \Eloquent
{
    protected $table = "recipe_ingredients";
    
    public function recipe()
    {
        return $this->belongsTo('\App\Models\Recipe');
    }
    
    public function ingredient()
    {
        return $this->belongsTo('\App\Models\Ingredient');
    }
    
    public function getNameAttribute()
    {
        return $this->ingredient->name;
    }
}