<?php

namespace App\Utils;

class CropEntropy extends \stojg\crop\CropEntropy
{
    public function resizeAndCrop($targetWidth, $targetHeight)
    {
        // First get the size that we can use to safely trim down the image without cropping any sides
        $crop = $this->getSafeResizeOffset($this->originalImage, $targetWidth, $targetHeight);

        // Get the offset for cropping the image further
        $offset = $this->getSpecialOffset($this->originalImage, $targetWidth, $targetHeight);
        $this->originalImage->setImagePage($targetWidth,$targetHeight,0,0);
       
        // Crop the image
        $this->originalImage->cropImage($targetWidth, $targetHeight, $offset['x'], $offset['y']);
        $this->originalImage->setImagePage($targetWidth,$targetHeight,0,0);
        
        return $this->originalImage;
    }
}