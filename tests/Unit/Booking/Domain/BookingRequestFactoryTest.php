<?php
namespace Tests\Unit\Booking\Domain;

use App\Booking\Domain\BookingRequest;
use App\Booking\Domain\BookingRequestFactory;
use PHPUnit\Framework\TestCase;

class BookingRequestFactoryTest extends TestCase
{
    private BookingRequestFactory $factory;

    protected function setUp(): void
    {
        $this->factory = new BookingRequestFactory();
    }

    public function test_create_valid_booking_request(): void
    {
        $data = [
            'request_id' => 'request-1',
            'check_in' => '2026-01-01',
            'nights' => 2,
            'selling_rate' => 100.0,
            'margin' => 10.0
        ];

        $booking = $this->factory->createFromArray($data);

        $this->assertInstanceOf(BookingRequest::class, $booking);
        $this->assertEquals('request-1', $booking->getId());
        $this->assertEquals(2, $booking->getNights());
        $this->assertEquals(100.0, $booking->getSellingRate());
        $this->assertEquals(10.0, $booking->getMargin());
    }

    public function test_throw_invalid_argument_exception(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Error! Fields request_id, check_in are mandatory');

        $data = [
            'nights' => 2,
            'selling_rate' => 100.0,
            'margin' => 10.0
        ];

        $this->factory->createFromArray($data);
    }
}