<?php

namespace Tests\Unit\Booking\Application\Maximize;

use App\Booking\Application\Maximize\MaximizeBookingResponse;

class MaximizeBookingResponseMother
{
    public static function create(
        array $requestIds = ['test_1', 'test_2', 'test_3', 'test_4', 'test_5'],
        float $totalProfit = 0.01,
        float $average = 5.0,
        float $minimum = 3.0,
        float $maximum = 7.0
    ): MaximizeBookingResponse {
        return new MaximizeBookingResponse($requestIds, $totalProfit, $average, $minimum, $maximum);
    }
}