<?php

namespace App\Booking\Application\Stats;

use App\Booking\Domain\BookingRequestFactory;

class GetBookingStatsUseCase
{
    public function __construct(
        private readonly BookingRequestFactory $factory
    ) {}

    public function execute(array $bookingRequests): GetBookingStatsResponse
    {
        $profitsPerNight = array_map(
            fn (array $booking) => $this->factory->createFromArray($booking)->getProfitPerNight(),
            $bookingRequests
        );

        return new GetBookingStatsResponse(
            array_sum($profitsPerNight) / count($profitsPerNight),
            min($profitsPerNight),
            max($profitsPerNight)
        );
    }
}
