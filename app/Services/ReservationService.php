<?php

namespace App\Services;

use App\Models\Product;
use App\Models\ProductReservation;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

class ReservationService
{
    /**
     * Reservation duration in minutes
     */
    const RESERVATION_DURATION = 15;

    /**
     * Reserve a product
     */
    public function reserveProduct(Product $product, int $quantity): ?ProductReservation
    {
        // Check product availability
        if (!$this->isQuantityAvailable($product, $quantity)) {
            Log::warning('Product reservation failed - quantity not available', [
                'product_id' => $product->id,
                'requested_quantity' => $quantity,
                'available_quantity' => $this->getAvailableQuantity($product),
                'user_id' => Auth::id()
            ]);
            return null;
        }

        // Remove expired reservations
        $this->clearExpiredReservations();

        // Create new reservation
        return ProductReservation::create([
            'product_id' => $product->id,
            'user_id' => Auth::id(),
            'session_id' => Session::getId(),
            'quantity' => $quantity,
            'expires_at' => now()->addMinutes(self::RESERVATION_DURATION)
        ]);
    }

    /**
     * Check if requested quantity is available
     * 
     * TODO: Implement proper stock tracking system
     * - Add stock field to products table
     * - Consider active reservations in stock calculation
     * - Add low stock alerts and notifications
     * - Implement stock reservation logic
     */
    private function isQuantityAvailable(Product $product, int $requestedQuantity): bool
    {
        // For now, always return true since we're not tracking stock
        // This will be replaced with actual stock checking logic
        return true;
    }

    /**
     * Get available quantity for a product
     * 
     * TODO: Implement proper stock calculation
     * - Subtract active reservations from total stock
     * - Consider pending orders in calculation
     * - Add safety stock levels
     * - Implement real-time stock updates
     */
    private function getAvailableQuantity(Product $product): int
    {
        // For now, return a large number since we're not tracking stock
        // This will be replaced with actual stock calculation
        return 999;
    }

    /**
     * Remove expired reservations
     */
    public function clearExpiredReservations(): void
    {
        try {
            $deletedCount = ProductReservation::where('expires_at', '<=', now())->delete();
            
            if ($deletedCount > 0) {
                Log::info('Expired reservations cleared', [
                    'deleted_count' => $deletedCount,
                    'method' => __METHOD__
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Failed to clear expired reservations', [
                'error' => $e->getMessage(),
                'method' => __METHOD__
            ]);
        }
    }

    /**
     * Extend reservation
     */
    public function extendReservation(ProductReservation $reservation): bool
    {
        if ($reservation->isExpired()) {
            Log::warning('Cannot extend expired reservation', [
                'reservation_id' => $reservation->id,
                'expires_at' => $reservation->expires_at,
                'user_id' => Auth::id()
            ]);
            return false;
        }

        try {
            $result = $reservation->update([
                'expires_at' => now()->addMinutes(self::RESERVATION_DURATION)
            ]);
            
            if ($result) {
                Log::info('Reservation extended successfully', [
                    'reservation_id' => $reservation->id,
                    'new_expires_at' => $reservation->expires_at,
                    'user_id' => Auth::id()
                ]);
            }
            
            return $result;
        } catch (\Exception $e) {
            Log::error('Failed to extend reservation', [
                'reservation_id' => $reservation->id,
                'error' => $e->getMessage(),
                'user_id' => Auth::id()
            ]);
            
            return false;
        }
    }

    /**
     * Cancel reservation
     */
    public function cancelReservation(ProductReservation $reservation): bool
    {
        try {
            $result = $reservation->delete();
            
            if ($result) {
                Log::info('Reservation cancelled successfully', [
                    'reservation_id' => $reservation->id,
                    'product_id' => $reservation->product_id,
                    'user_id' => Auth::id()
                ]);
            }
            
            return $result;
        } catch (\Exception $e) {
            Log::error('Failed to cancel reservation', [
                'reservation_id' => $reservation->id,
                'error' => $e->getMessage(),
                'user_id' => Auth::id()
            ]);
            
            return false;
        }
    }

    /**
     * Get active reservations for user or session
     */
    public function getUserReservations()
    {
        try {
            $query = ProductReservation::with('product')->active();

            if (Auth::check()) {
                $query->where('user_id', Auth::id());
            } else {
                $query->where('session_id', Session::getId());
            }

            $reservations = $query->get();
            
            Log::info('User reservations retrieved', [
                'count' => $reservations->count(),
                'user_id' => Auth::id(),
                'session_id' => Session::getId()
            ]);
            
            return $reservations;
        } catch (\Exception $e) {
            Log::error('Failed to get user reservations', [
                'error' => $e->getMessage(),
                'user_id' => Auth::id(),
                'session_id' => Session::getId()
            ]);
            
            return collect();
        }
    }

    /**
     * Convert session reservations to user after login
     */
    public function convertSessionReservationsToUser(): void
    {
        if (!Auth::check()) {
            return;
        }

        try {
            $convertedCount = ProductReservation::where('session_id', Session::getId())
                ->update([
                    'user_id' => Auth::id(),
                    'session_id' => null
                ]);
            
            if ($convertedCount > 0) {
                Log::info('Session reservations converted to user', [
                    'converted_count' => $convertedCount,
                    'user_id' => Auth::id(),
                    'session_id' => Session::getId()
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Failed to convert session reservations', [
                'error' => $e->getMessage(),
                'user_id' => Auth::id(),
                'session_id' => Session::getId()
            ]);
        }
    }
} 