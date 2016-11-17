<?php

namespace App\Models;

class Recipe extends \Eloquent
{
    protected $table = 'recipes';
    
    public function ingredients()
    {
        return $this->hasMany('\App\Model\Ingredient');
    }
    
    public function cusine()
    {
        return $this->hasOne('\App\Model\Cusine');
    }
    
    public function course()
    {
        return $this->hasOne('\App\Models\Course');
    }
}