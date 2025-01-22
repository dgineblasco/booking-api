<?php
namespace Tests\Unit\Booking\Domain;

use App\Booking\Domain\BookingRequest;
use App\Booking\Domain\ValueObject\CheckInDate;
use App\Booking\Domain\ValueObject\Margin;
use App\Booking\Domain\ValueObject\Nights;
use App\Booking\Domain\ValueObject\RequestId;
use App\Booking\Domain\ValueObject\SellingRate;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

class BookingRequestTest extends TestCase
{
    public function test_create_valid_booking_request(): void
    {
        $checkIn = new DateTimeImmutable('2026-01-01');

        $booking = new BookingRequest(
            new RequestId('request-1'),
            new CheckInDate('2026-01-01'),
            new Nights(2),
            new SellingRate(100.0),
            new Margin(10.0)
        );

        $expectedCheckOut = $checkIn->modify('+2 days');

        $this->assertEquals('request-1', $booking->getId());
        $this->assertEquals(2, $booking->getNights());
        $this->assertEquals(100.0, $booking->getSellingRate());
        $this->assertEquals(10.0, $booking->getMargin());
        $this->assertEquals(5.0, $booking->getProfitPerNight());
        $this->assertEquals(10.0, $booking->getTotalProfit());
        $this->assertEquals($expectedCheckOut, $booking->getCheckOut());
    }

    public function test_throw_exception_for_invalid_selling_rate(): void {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Selling rate must be positive');

        new BookingRequest(
            new RequestId('request-1'),
            new CheckInDate('2026-01-01'),
            new Nights(2),
            new SellingRate(-100.0),
            new Margin(10.0)
        );
    }

    public function test_throw_exception_for_invalid_selling_margin(): void {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Margin must be between 0 and 100');

        new BookingRequest(
            new RequestId('request-1'),
            new CheckInDate('2026-01-01'),
            new Nights(2),
            new SellingRate(100.0),
            new Margin(-10.0)
        );
    }

    public function test_throw_exception_for_invalid_selling_night(): void {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Nights must be positive');

        new BookingRequest(
            new RequestId('request-1'),
            new CheckInDate('2026-01-01'),
            new Nights(0),
            new SellingRate(100.0),
            new Margin(10.0)
        );
    }
}