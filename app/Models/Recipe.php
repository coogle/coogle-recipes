<?php

namespace App\Models;

class Recipe extends \Eloquent
{
    protected $table = 'recipes';
    
    public function ingredients()
    {
        return $this->hasMany('App\Models\Recipe\Ingredient');
    }
    
    public function cusine()
    {
        return $this->hasOne('\App\Models\Cusine');
    }
    
    public function course()
    {
        return $this->hasOne('\App\Models\Course');
    }
}