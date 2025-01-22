<?php

declare(strict_types=1);

namespace Tests\Unit\Booking\Domain\ValueObject;

use App\Booking\Domain\Exception\InvalidMarginException;
use App\Booking\Domain\ValueObject\Margin;
use PHPUnit\Framework\TestCase;

class MarginTest extends TestCase
{
    public function test_create_valid_margin(): void
    {
        $margin = new Margin(5);
        $this->assertEquals(5, $margin->value());
    }

    public function test_cannot_create_with_zero_margin(): void
    {
        $this->expectException(InvalidMarginException::class);
        new margin(0);
    }

    public function test_cannot_create_with_negative_margin(): void
    {
        $this->expectException(InvalidMarginException::class);
        new margin(-1);
    }
}
