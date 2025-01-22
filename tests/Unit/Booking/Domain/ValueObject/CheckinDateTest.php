<?php

declare(strict_types=1);

namespace Tests\Unit\Booking\Domain\ValueObject;

use App\Booking\Domain\Exception\InvalidCheckInDateException;
use App\Booking\Domain\ValueObject\CheckInDate;
use PHPUnit\Framework\TestCase;

class CheckinDateTest extends TestCase
{
    public function test_create_valid_checkin_date(): void
    {
        $checkinDate = new CheckInDate('2026-01-01');
        $this->assertEquals('2026-01-01', $checkinDate->value()->format('Y-m-d'));
    }

    public function test_cannot_create_with_checkin_date(): void
    {
        $this->expectException(InvalidCheckInDateException::class);
        new CheckInDate('2021-01-01');
    }
}
