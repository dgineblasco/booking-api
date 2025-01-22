<?php

declare(strict_types=1);

namespace Tests\Unit\Booking\Application\Stats;

use App\Booking\Application\Stats\GetBookingStatsQuery;

class GetBookingStatsQueryMother
{
    public static function create(array $bookings = []): GetBookingStatsQuery
    {
        return new GetBookingStatsQuery($bookings ?? [
            [
                'request_id' => 'default-id',
                'check_in' => '2026-01-01',
                'nights' => 2,
                'selling_rate' => 100.0,
                'margin' => 10.0
            ]
        ]);
    }

    public static function withTwoBookings(): GetBookingStatsQuery
    {
        return self::create([
            [
                'request_id' => '1',
                'check_in' => '2026-01-01',
                'nights' => 2,
                'selling_rate' => 100.0,
                'margin' => 10.0
            ],
            [
                'request_id' => '2',
                'check_in' => '2026-01-03',
                'nights' => 3,
                'selling_rate' => 150.0,
                'margin' => 15.0
            ]
        ]);
    }
}
