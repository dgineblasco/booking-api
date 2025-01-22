<?php

declare(strict_types=1);

namespace Tests\Unit\Booking\Domain\ValueObject;

use App\Booking\Domain\Exception\InvalidNightsException;
use App\Booking\Domain\ValueObject\Nights;
use PHPUnit\Framework\TestCase;

class NightsTest extends TestCase
{
    public function test_create_valid_nights(): void
    {
        $nights = new Nights(5);
        $this->assertEquals(5, $nights->value());
    }

    public function test_cannot_create_with_zero_nights(): void
    {
        $this->expectException(InvalidNightsException::class);
        new Nights(0);
    }

    public function test_cannot_create_with_negative_nights(): void
    {
        $this->expectException(InvalidNightsException::class);
        new Nights(-1);
    }
}
