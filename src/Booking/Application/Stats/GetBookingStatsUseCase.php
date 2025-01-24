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

        $bookings->calculateMetrics();

        return new GetBookingStatsResponse(
            $bookings->getAverageNight(),
            $bookings->getMinNight(),
            $bookings->getMaxNight(),
        );
    }
}
