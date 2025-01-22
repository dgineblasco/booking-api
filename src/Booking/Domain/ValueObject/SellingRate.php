<?php

namespace App\Booking\Domain\ValueObject;

use App\Booking\Domain\Exception\InvalidSellingRateException;

final readonly class SellingRate
{
    public function __construct(private float $value)
    {
        $this->validate($value);
    }

    private function validate(float $value): void
    {
        if ($value <= 0) {
            throw new InvalidSellingRateException('Selling rate must be positive');
        }
    }

    public function value(): float
    {
        return $this->value;
    }
}