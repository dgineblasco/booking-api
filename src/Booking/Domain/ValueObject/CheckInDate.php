<?php

declare(strict_types=1);

namespace App\Booking\Domain\ValueObject;

use App\Booking\Domain\Exception\InvalidCheckInDateException;
use DateTimeImmutable;

final readonly class CheckInDate
{
    private DateTimeImmutable $value;

    public function __construct(string $date)
    {
        $this->value = new DateTimeImmutable($date);
        $this->validate();
    }

    private function validate(): void
    {
        if ($this->value < new DateTimeImmutable('today')) {
            throw new InvalidCheckInDateException('Check-in date cannot be in the past');
        }
    }

    public function value(): DateTimeImmutable
    {
        return $this->value;
    }
}
