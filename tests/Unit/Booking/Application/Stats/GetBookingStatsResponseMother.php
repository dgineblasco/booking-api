<?php

namespace Tests\Unit\Booking\Application\Stats;

use App\Booking\Application\Stats\GetBookingStatsResponse;

class GetBookingStatsResponseMother
{
    public static function create(
        float $average = 5.0,
        float $minimum = 3.0,
        float $maximum = 7.0
    ): GetBookingStatsResponse {
        return new GetBookingStatsResponse($average, $minimum, $maximum);
    }
}