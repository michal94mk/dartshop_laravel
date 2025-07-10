<?php

namespace App\Services;

use App\Models\Product;
use App\Models\ProductReservation;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
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
     */
    private function isQuantityAvailable(Product $product, int $requestedQuantity): bool
    {
        // For now, always return true since we're not tracking stock
        return true;
    }

    /**
     * Get available quantity for a product
     */
    private function getAvailableQuantity(Product $product): int
    {
        // For now, return a large number since we're not tracking stock
        return 999;
    }

    /**
     * Remove expired reservations
     */
    public function clearExpiredReservations(): void
    {
        ProductReservation::where('expires_at', '<=', now())->delete();
    }

    /**
     * Extend reservation
     */
    public function extendReservation(ProductReservation $reservation): bool
    {
        if ($reservation->isExpired()) {
            return false;
        }

        return $reservation->update([
            'expires_at' => now()->addMinutes(self::RESERVATION_DURATION)
        ]);
    }

    /**
     * Cancel reservation
     */
    public function cancelReservation(ProductReservation $reservation): bool
    {
        return $reservation->delete();
    }

    /**
     * Get active reservations for user or session
     */
    public function getUserReservations()
    {
        $query = ProductReservation::with('product')->active();

        if (Auth::check()) {
            $query->where('user_id', Auth::id());
        } else {
            $query->where('session_id', Session::getId());
        }

        return $query->get();
    }

    /**
     * Convert session reservations to user after login
     */
    public function convertSessionReservationsToUser(): void
    {
        if (!Auth::check()) {
            return;
        }

        ProductReservation::where('session_id', Session::getId())
            ->update([
                'user_id' => Auth::id(),
                'session_id' => null
            ]);
    }
} 