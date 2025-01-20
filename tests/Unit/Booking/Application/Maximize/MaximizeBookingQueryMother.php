<?php

namespace Tests\Unit\Booking\Application\Maximize;

use App\Booking\Application\Maximize\MaximizeBookingQuery;

class MaximizeBookingQueryMother
{
    public static function create(array $bookings = []): MaximizeBookingQuery
    {
        return new MaximizeBookingQuery($bookings ?? [
            [
                'request_id' => 'default-id',
                'check_in' => '2024-01-01',
                'nights' => 2,
                'selling_rate' => 100.0,
                'margin' => 10.0
            ]
        ]);
    }

    public static function withTwoBookings(): MaximizeBookingQuery
    {
        return self::create([
            [
                'request_id' => '1',
                'check_in' => '2024-01-01',
                'nights' => 2,
                'selling_rate' => 100.0,
                'margin' => 10.0
            ],
            [
                'request_id' => '2',
                'check_in' => '2024-01-03',
                'nights' => 3,
                'selling_rate' => 150.0,
                'margin' => 15.0
            ]
        ]);
    }
}