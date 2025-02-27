<?php

declare(strict_types=1);

namespace App\Booking\Application\Maximize;

final readonly class MaximizeBookingQuery
{
    public function __construct(
        private array $bookings
    ) {
    }

    public function getBookings(): array
    {
        return $this->bookings;
    }
}
