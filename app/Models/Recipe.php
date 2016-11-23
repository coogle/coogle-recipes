<?php

namespace App\Models;

use App\Recipe\Exporter;
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
    
    static public function exportAll($uri = null)
    {
        $recipes = static::all();
        
        $recipeExporter = new Exporter();
        
        if(is_null($uri)) {
            $exportUri = tmpnam(storage_path(), "export_");
        } else {
            $exportUri = $uri;
        }
        
        $writer = null;
        
        foreach($recipes as $recipe) {
        
            $recipeExporter->setTitle($recipe->title);
        
            foreach($recipe->ingredients as $ingredient) {
                $recipeExporter->addIngredient($ingredient->quantity, $ingredient->measurement, $ingredient->name);
            }
        
            foreach(explode("\n", $recipe->directions) as $direction) {
                $recipeExporter->addDirection(trim($direction));
            }
        
            $writer = $recipeExporter->toRecipeML($writer, $exportUri);
            $recipeExporter->reset();
        }
        
        $writer->endElement();
        $writer->endDocument();
        
        if(is_null($uri)) {
            return $exportUri;
        } else {
            return true;
        }
    }
}