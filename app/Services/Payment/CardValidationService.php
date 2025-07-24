<?php

namespace App\Services\Payment;

class CardValidationService
{
    /**
     * Validate credit card number using the Luhn algorithm.
     *
     * @param string $cardNumber
     * @return bool
     */
    public function validateCardNumber(string $cardNumber): bool
    {
        // Remove spaces and dashes
        $cardNumber = preg_replace('/[\s\-]/', '', $cardNumber);
        
        // Check length (13-19 digits)
        if (!preg_match('/^[0-9]{13,19}$/', $cardNumber)) {
            return false;
        }
        
        // Luhn algorithm (checksum validation)
        return $this->luhnCheck($cardNumber);
    }

    /**
     * Luhn algorithm implementation
     */
    private function luhnCheck(string $cardNumber): bool
    {
        $sum = 0;
        $length = strlen($cardNumber);
        $parity = $length % 2;
        
        for ($i = 0; $i < $length; $i++) {
            $digit = (int) $cardNumber[$i];
            
            if ($i % 2 == $parity) {
                $digit *= 2;
                if ($digit > 9) {
                    $digit -= 9;
                }
            }
            
            $sum += $digit;
        }
        
        return ($sum % 10) == 0;
    }

    /**
     * Get test card numbers for different providers.
     *
     * @return array
     */
    public function getTestCards(): array
    {
        return [
            'visa' => '4242424242424242',
            'visa_debit' => '4000056655665556',
            'mastercard' => '5555555555554444',
            'mastercard_debit' => '5200828282828210',
            'amex' => '378282246310005',
            'discover' => '6011111111111117',
            'diners' => '30569309025904',
            'jcb' => '3530111333300000'
        ];
    }

    /**
     * Detect card brand from number.
     *
     * @param string $cardNumber
     * @return string|null
     */
    public function detectCardBrand(string $cardNumber): ?string
    {
        $cardNumber = preg_replace('/[\s\-]/', '', $cardNumber);
        
        $patterns = [
            'visa' => '/^4[0-9]{12}(?:[0-9]{3})?$/',
            'mastercard' => '/^5[1-5][0-9]{14}$/',
            'amex' => '/^3[47][0-9]{13}$/',
            'discover' => '/^6(?:011|5[0-9]{2})[0-9]{12}$/',
            'diners' => '/^3[0689][0-9]{11}$/',
            'jcb' => '/^(?:2131|1800|35\d{3})\d{11}$/'
        ];

        foreach ($patterns as $brand => $pattern) {
            if (preg_match($pattern, $cardNumber)) {
                return $brand;
            }
        }

        return null;
    }

    /**
     * Format card number with spaces for display.
     *
     * @param string $cardNumber
     * @return string
     */
    public function formatCardNumber(string $cardNumber): string
    {
        $cardNumber = preg_replace('/[\s\-]/', '', $cardNumber);
        
        // Add spaces every 4 digits
        return chunk_split($cardNumber, 4, ' ');
    }

    /**
     * Mask card number for secure display.
     *
     * @param string $cardNumber
     * @return string
     */
    public function maskCardNumber(string $cardNumber): string
    {
        $cardNumber = preg_replace('/[\s\-]/', '', $cardNumber);
        
        if (strlen($cardNumber) < 4) {
            return str_repeat('*', strlen($cardNumber));
        }
        
        $lastFour = substr($cardNumber, -4);
        $masked = str_repeat('*', strlen($cardNumber) - 4) . $lastFour;
        
        return $this->formatCardNumber($masked);
    }
} 