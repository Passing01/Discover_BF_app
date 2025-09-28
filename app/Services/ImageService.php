<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image as ImageFacade;

class ImageService
{
    public function uploadHotelImage(UploadedFile $file, $hotelId, $isMain = false)
    {
        $filename = uniqid() . '.' . $file->getClientOriginalExtension();
        $path = "hotels/{$hotelId}/" . ($isMain ? 'main/' : 'gallery/') . $filename;
        
        $image = ImageFacade::make($file);
        
        // Redimensionner l'image si nécessaire
        $image->resize(1200, 800, function ($constraint) {
            $constraint->aspectRatio();
            $constraint->upsize();
        });
        
        // Enregistrer l'image
        Storage::put($path, (string) $image->encode());
        
        return $path;
    }
    
    public function deleteImage($path)
    {
        if (Storage::exists($path)) {
            Storage::delete($path);
            return true;
        }
        return false;
    }
}
