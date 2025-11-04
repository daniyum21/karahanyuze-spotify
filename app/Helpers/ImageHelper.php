<?php

namespace App\Helpers;

class ImageHelper
{
    public static function getImageUrl($profilePicture, $type = 'pictures')
    {
        if (empty($profilePicture)) {
            return asset('placeholder.svg');
        }

        $filename = basename($profilePicture);
        
        // Try .jpg first, then .jpeg
        $jpgPath = storage_path("app/public/{$type}/{$filename}.jpg");
        $jpegPath = storage_path("app/public/{$type}/{$filename}.jpeg");
        
        if (file_exists($jpgPath)) {
            return asset("storage/{$type}/{$filename}.jpg");
        } elseif (file_exists($jpegPath)) {
            return asset("storage/{$type}/{$filename}.jpeg");
        }
        
        return asset('placeholder.svg');
    }
}

