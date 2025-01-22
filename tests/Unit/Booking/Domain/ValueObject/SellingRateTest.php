<?php
namespace Tests\Unit\Booking\Domain\ValueObject;

use App\Booking\Domain\BookingRequest;
use App\Booking\Domain\Exception\InvalidSellingRateException;
use App\Booking\Domain\ValueObject\SellingRate;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

class SellingRateTest extends TestCase
{
    public function test_create_valid_selling_rate(): void
    {
        $sellingRate = new SellingRate(5);
        $this->assertEquals(5, $sellingRate->value());
    }

    public function test_cannot_create_with_zero_selling_rate(): void
    {
        $this->expectException(InvalidSellingRateException::class);
        new SellingRate(0);
    }

    public function test_cannot_create_with_negative_selling_rate(): void
    {
        $this->expectException(InvalidSellingRateException::class);
        new SellingRate(-1);
    }
}