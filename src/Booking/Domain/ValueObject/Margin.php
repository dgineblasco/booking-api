<?php

namespace App\Booking\Domain\ValueObject;

use App\Booking\Domain\Exception\InvalidMarginException;

final readonly class Margin
{
    public function __construct(private float $value)
    {
        $this->validate($value);
    }

    private function validate(float $value): void
    {
        if ($value <= 0 || $value > 100) {
            throw new InvalidMarginException('Margin must be between 0 and 100');
        }
    }

    public function value(): float
    {
        return $this->value;
    }
}