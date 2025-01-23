<?php

declare(strict_types=1);

namespace App\Booking\Application\Stats;

use App\Booking\Application\Creator\BookingRequestCreator;
use App\Booking\Domain\BookingRequest;

class GetBookingStatsUseCase
{
    public function __construct(
        private readonly BookingRequestCreator $bookingRequestCreator
    ) {
    }

    public function execute(array $rawBookings): GetBookingStatsResponse
    {
        $bookings = $this->bookingRequestCreator->createCollection($rawBookings);

        if ($bookings->isEmpty()) {
            return new GetBookingStatsResponse(0, 0, 0);
        }

        $profitsPerNight = $bookings->map(
            fn (BookingRequest $booking) => $booking->getProfitPerNight()
        );

        return new GetBookingStatsResponse(
            array_sum($profitsPerNight) / count($profitsPerNight),
            min($profitsPerNight),
            max($profitsPerNight)
        );
    }
}
