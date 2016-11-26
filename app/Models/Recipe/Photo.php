<?php

namespace App\Models\Recipe;

use App\Utils\CropEntropy;

class Photo extends \Eloquent
{
    protected $table = 'recipe_photos';
    
    public function recipe()
    {
        return $this->belongsTo('App\Models\Recipe');
    }
    
    public function setPhotoDataAttribute($data)
    {
        if($data instanceof \Imagick) {
            $imagick = $data;
        } else {
            $imagick = new \Imagick();
            $imagick->readImageBlob($data);
            $imagick->setimageresolution(72, 72);
        }
        
        if(($imagick->getImageWidth() * $imagick->getImageHeight()) > 786432) {
            
            $imagick->scaleImage(1024, 0);
            
            if($imagick->getImageWidth() * $imagick->getImageHeight() > 786432) {
                $cropper = new CropEntropy($imagick);
                $imagick = $cropper->resizeAndCrop(1024, 768);
            }
        }
        
        $imagick->setImageFormat('png');
        
        $this->attributes['mimetype'] = $imagick->getImageMimeType();
        $this->attributes['photo'] = $imagick->getImageBlob();
        $this->attributes['width'] = $imagick->getImageWidth();
        $this->attributes['height'] = $imagick->getImageHeight();
    }
    
    public function getPhotoObjectAttribute()
    {
        if(empty($this->photo)) {
            return null;
        }
        
        $imagick = new \Imagick();
        $imagick->readImageBlob($this->photo);
        $imagick->setimageresolution(72, 72);
        
        return $imagick;
    }
    
    public function cropTo($width, $height)
    {
        $cropper = new CropEntropy($this->photo_object);
        $imagick = $cropper->resizeAndCrop($width, $height);
        
        $imagick->setImageFormat('png');
        
        $this->attributes['mimetype'] = $imagick->getImageMimeType();
        $this->attributes['photo'] = $imagick->getImageBlob();
        $this->attributes['width'] = $imagick->getImageWidth();
        $this->attributes['height'] = $imagick->getImageHeight();
    }
}