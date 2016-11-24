<?php

namespace App\Utils;

class Gzip
{
    static public function gzCompressFile($source, $compression = 9) 
    {
        $destination = "{$source}.gz";
        
        $retval = false;
        
        $gzipFr = gzopen($destination, "wb{$compression}");
        
        if(!$gzipFr) {
            throw new \Exception("Failed to open destination compression file");
        }
        
        $inputFr = null;
        
        if(is_resource($source)) {

            if(get_resource_type($source) != 'stream') {
                throw new \Exception("Invalid Input Source");
            }
            
            $inputFr = $source;
            
        } elseif(is_string($source)) {
            
            if(!is_readable($source)) {
                throw new \Exception("Can not open file");
            }
            
            $inputFr = fopen($source, 'rb');
            
        } else {
            throw new \Exception("Must pass stream or stream resource");
        }
        
        while(!feof($inputFr)) {
            gzwrite($gzipFr, fread($inputFr, 1024 * 512));
        }
        
        fclose($inputFr);
        gzclose($gzipFr);
        
        return $destination;
    }
}