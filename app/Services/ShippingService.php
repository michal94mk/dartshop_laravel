<?php

namespace App\Services;

class ShippingService
{
    /**
     * Get all available shipping methods
     *
     * @return array
     */
    public function getShippingMethods(): array
    {
        return config('shipping.methods', []);
    }

    /**
     * Get shipping method details by key
     *
     * @param string $methodKey
     * @return array|null
     */
    public function getShippingMethod(string $methodKey): ?array
    {
        $methods = $this->getShippingMethods();
        return $methods[$methodKey] ?? null;
    }

    /**
     * Calculate shipping cost for given method and cart total
     *
     * @param string $methodKey
     * @param float $cartTotal
     * @return float
     */
    public function calculateShippingCost(string $methodKey, float $cartTotal): float
    {
        $method = $this->getShippingMethod($methodKey);
        
        if (!$method) {
            return 0.00;
        }

        $shippingCost = $method['cost'];
        $freeShippingThreshold = config('shipping.free_shipping_threshold', 0);

        // Check if cart qualifies for free shipping
        if ($freeShippingThreshold > 0 && $cartTotal >= $freeShippingThreshold && $methodKey !== 'express') {
            return 0.00;
        }

        return $shippingCost;
    }

    /**
     * Get shipping methods with calculated costs for given cart total
     *
     * @param float $cartTotal
     * @return array
     */
    public function getShippingMethodsWithCosts(float $cartTotal): array
    {
        $methods = $this->getShippingMethods();
        $methodsWithCosts = [];

        foreach ($methods as $key => $method) {
            $cost = $this->calculateShippingCost($key, $cartTotal);
            $isFree = $cost == 0 && $method['cost'] > 0;

            $methodsWithCosts[$key] = array_merge($method, [
                'calculated_cost' => $cost,
                'original_cost' => $method['cost'],
                'is_free' => $isFree
            ]);
        }

        return $methodsWithCosts;
    }

    /**
     * Get default shipping method
     *
     * @return string
     */
    public function getDefaultMethod(): string
    {
        return config('shipping.default_method', 'courier');
    }

    /**
     * Check if shipping method exists
     *
     * @param string $methodKey
     * @return bool
     */
    public function isValidMethod(string $methodKey): bool
    {
        return array_key_exists($methodKey, $this->getShippingMethods());
    }

    /**
     * Get free shipping threshold
     *
     * @return float
     */
    public function getFreeShippingThreshold(): float
    {
        return config('shipping.free_shipping_threshold', 0);
    }
} 