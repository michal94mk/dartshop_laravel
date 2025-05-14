<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ImageService
{
    /**
     * Upload an image to storage
     *
     * @param UploadedFile $image
     * @param string $name Base name for the image
     * @param string $folder Folder within storage/images
     * @return string|null The path to the uploaded image, or null on failure
     */
    public function uploadImage(UploadedFile $image, string $name, string $folder = ''): ?string
    {
        if (!$image->isValid()) {
            return null;
        }
        
        $filename = Str::slug($name) . '-' . time() . '.' . $image->getClientOriginalExtension();
        $path = trim("storage/images/{$folder}", '/');
        
        Log::info('Uploading image', [
            'original_name' => $image->getClientOriginalName(),
            'mime_type' => $image->getMimeType(),
            'size' => $image->getSize(),
            'destination' => $path . '/' . $filename
        ]);
        
        try {
            $image->move(public_path($path), $filename);
            return $path . '/' . $filename;
        } catch (\Exception $e) {
            Log::error('Error uploading image', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return null;
        }
    }
    
    /**
     * Delete an image from storage
     *
     * @param string|null $imagePath
     * @return bool
     */
    public function deleteImage(?string $imagePath): bool
    {
        if (!$imagePath) {
            return false;
        }
        
        $fullPath = public_path($imagePath);
        
        if (file_exists($fullPath)) {
            @unlink($fullPath);
            return true;
        }
        
        return false;
    }
} 