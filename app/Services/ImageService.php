<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class ImageService
{
    /**
     * Get properly formatted image URL for a product.
     */
    public function getProductImageUrl(?string $imageUrl, int $productId = null, string $productName = null): ?string
    {
        if (!$imageUrl) {
            if ($productId && $productName) {
                Log::debug('Product image is null', [
                    'product_id' => $productId,
                    'product_name' => $productName
                ]);
            }
            return null;
        }
        
        if ($productId && $productName) {
            Log::debug('Processing product image', [
                'product_id' => $productId,
                'product_name' => $productName,
                'original_image_url' => $imageUrl
            ]);
        }
        
        // Jeśli to pełny URL (http/https)
        if (str_starts_with($imageUrl, 'http://') || str_starts_with($imageUrl, 'https://')) {
            return $imageUrl;
        }
        
        // Normalizuj separatory ścieżek
        $path = str_replace('\\', '/', $imageUrl);
        
        // Usuń ewentualne początkowe slashe
        $path = ltrim($path, '/');
        
        // Jeśli ścieżka zaczyna się od storage/
        if (str_starts_with($path, 'storage/')) {
            $path = substr($path, 8); // usuń 'storage/'
        }
        
        // Jeśli ścieżka zaczyna się od products/ lub jest samą nazwą pliku
        if (str_starts_with($path, 'products/') || !str_contains($path, '/')) {
            // Sprawdź czy plik istnieje w storage/app/public/products
            $storagePath = str_starts_with($path, 'products/') ? $path : 'products/' . $path;
            
            if ($productId) {
                Log::debug('Checking storage path', [
                    'product_id' => $productId,
                    'storage_path' => $storagePath,
                    'full_path' => Storage::disk('public')->path($storagePath),
                    'exists' => Storage::disk('public')->exists($storagePath)
                ]);
            }
            
            if (Storage::disk('public')->exists($storagePath)) {
                $url = url('storage/' . $storagePath);
                if ($productId) {
                    Log::debug('Found file in storage', [
                        'product_id' => $productId,
                        'storage_path' => $storagePath,
                        'url' => $url
                    ]);
                }
                return $url;
            }
        }
        
        // Sprawdź czy plik istnieje w public/img
        $imgPath = 'img/' . basename($path);
        $publicPath = public_path($imgPath);
        
        if ($productId) {
            Log::debug('Checking public path', [
                'product_id' => $productId,
                'img_path' => $imgPath,
                'public_path' => $publicPath,
                'exists' => file_exists($publicPath)
            ]);
        }
        
        if (file_exists($publicPath)) {
            $url = url($imgPath);
            if ($productId) {
                Log::debug('Found file in public/img', [
                    'product_id' => $productId,
                    'img_path' => $imgPath,
                    'url' => $url
                ]);
            }
            return $url;
        }
        
        // Jeśli nie znaleziono pliku, zwróć ścieżkę do storage/products
        $fallbackUrl = url('storage/products/' . basename($path));
        if ($productId) {
            Log::debug('Using fallback URL', [
                'product_id' => $productId,
                'fallback_url' => $fallbackUrl,
                'original_path' => $path
            ]);
        }
        return $fallbackUrl;
    }
} 