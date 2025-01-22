<?php

declare(strict_types=1);

namespace App\Booking\Application\Stats;

final readonly class GetBookingStatsQuery
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
