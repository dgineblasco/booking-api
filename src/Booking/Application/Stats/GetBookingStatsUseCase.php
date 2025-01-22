<?php

namespace App\Booking\Application\Stats;

use App\Booking\Application\Creator\BookingRequestCreator;
use App\Booking\Domain\BookingRequest;

class GetBookingStatsUseCase
{
    public function __construct(
        private readonly BookingRequestCreator $bookingRequestCreator
    ) {}

    public function execute(array $rawBookings): GetBookingStatsResponse
    {
        $bookings = $this->bookingRequestCreator->createCollection($rawBookings);

        $profitsPerNight = $bookings->map(
            fn(BookingRequest $booking) => $booking->getProfitPerNight()
        );

        return new GetBookingStatsResponse(
            $this->calculateAverageProfit($profitsPerNight),
            empty($profitsPerNight) ? 0 : min($profitsPerNight),
            empty($profitsPerNight) ? 0 : max($profitsPerNight)
        );
    }

    private function calculateAverageProfit(array $profitsPerNight): float
    {
        return empty($profitsPerNight) ? 0 : array_sum($profitsPerNight) / count($profitsPerNight);
    }
}
