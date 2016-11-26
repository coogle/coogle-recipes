<?php

namespace App\Models;

use App\Recipe\Exporter;
use stojg\crop\CropEntropy;
use App\Models\Recipe\Photo;
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
    
    public function photos()
    {
        return $this->hasMany('App\Models\Recipe\Photo');
    }
    
    public function hasPhoto()
    {
        return ($this->photos()->count() > 0);
    }
    
    public function getPhotoByResolution($width, $height)
    {
        $photo = $this->photos()
                      ->where('width', '=', $width)
                      ->where('height', '=', $height)
                      ->limit(1)
                      ->first();
        
        if($photo instanceof Photo) {
            return $photo;
        }
        
        $largestPhoto = $this->photos()
                             ->orderBy('width', 'desc')
                             ->orderBy('height', 'desc')
                             ->limit(1)
                             ->first();
        
        
        if(!$largestPhoto instanceof Photo) {
            return null;
        }
        
        $newPhoto = $largestPhoto->replicate();

        $newPhoto->cropTo($width, $height);
        $newPhoto->save();
        
        return $newPhoto;
    }
    
    static public function exportAll($uri = null)
    {
        $exportUri = null;
        $writer = null;
        
        static::chunk(200, function($recipes) use ($uri, &$exportUri, &$writer) {
            $recipeExporter = new Exporter();
            
            if(is_null($uri)) {
                $exportUri = tmpnam(storage_path(), "export_");
            } else {
                $exportUri = $uri;
            }
            
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
                $writer->flush();
            }
            
        });
            
        $writer->endElement();
        $writer->endDocument();
                    
        if(is_null($uri)) {
            return $exportUri;
        } else {
            return true;
        }
   }
}