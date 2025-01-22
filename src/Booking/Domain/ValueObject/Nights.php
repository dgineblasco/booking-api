<?php

declare(strict_types=1);

namespace App\Booking\Domain\ValueObject;

use App\Booking\Domain\Exception\InvalidNightsException;

final readonly class Nights
{
    public function __construct(private int $value)
    {
        $this->validate($value);
    }

    private function validate(int $value): void
    {
        if ($value <= 0) {
            throw new InvalidNightsException('Nights must be positive');
        }
    }

    public function value(): int
    {
        return $this->value;
    }
}
