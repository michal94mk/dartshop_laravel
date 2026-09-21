<?php

namespace App\Exceptions;

use Exception;

class PaymentAmountMismatchException extends Exception
{
    public function __construct(
        public readonly int $expectedCents,
        public readonly int $paidCents,
        string $message = 'Kwota płatności nie zgadza się z zamówieniem'
    ) {
        parent::__construct($message);
    }
}
