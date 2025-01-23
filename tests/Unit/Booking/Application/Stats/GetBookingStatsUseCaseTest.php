<?php

declare(strict_types=1);

namespace Tests\Unit\Booking\Application\Stats;

use App\Booking\Application\Creator\BookingRequestCreator;
use App\Booking\Application\Stats\GetBookingStatsResponse;
use App\Booking\Application\Stats\GetBookingStatsUseCase;
use App\Booking\Domain\BookingRequest;
use App\Booking\Domain\BookingRequestCollection;
use App\Booking\Domain\ValueObject\CheckInDate;
use App\Booking\Domain\ValueObject\Margin;
use App\Booking\Domain\ValueObject\Nights;
use App\Booking\Domain\ValueObject\RequestId;
use App\Booking\Domain\ValueObject\SellingRate;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class GetBookingStatsUseCaseTest extends TestCase
{
    private BookingRequestCreator|MockObject $creator;

    private GetBookingStatsUseCase $useCase;

    protected function setUp(): void
    {
        $this->creator = $this->createMock(BookingRequestCreator::class);
        $this->useCase = new GetBookingStatsUseCase($this->creator);
    }

    public function test_calculate_stats_for_bookings(): void
    {
        $rawBookings = [
            [
                'request_id' => '1',
                'check_in' => '2026-01-01',
                'nights' => 2,
                'selling_rate' => 100.0,
                'margin' => 10.0
            ]
        ];

        /*** TODO: Create Mocks or Mothers instead ***/
        $collection = BookingRequestCollection::create(
            new BookingRequest(
                new RequestId($rawBookings[0]['request_id']),
                new CheckInDate($rawBookings[0]['check_in']),
                new Nights($rawBookings[0]['nights']),
                new SellingRate($rawBookings[0]['selling_rate']),
                new Margin($rawBookings[0]['margin'])
            )
        );

        $this->creator
            ->expects($this->once())
            ->method('createCollection')
            ->with($rawBookings)
            ->willReturn($collection);

        $response = $this->useCase->execute($rawBookings);

        $this->assertInstanceOf(GetBookingStatsResponse::class, $response);
        $responseData = $response->toArray();
        $this->assertEquals(5.0, $responseData['average']);
        $this->assertEquals(5.0, $responseData['minimum']);
        $this->assertEquals(5.0, $responseData['maximum']);
    }

    public function test_calculate_stats_for_multiple_bookings(): void
    {
        $rawBookings = [
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
        ];

        /*** TODO: Create Mocks or Mothers instead ***/
        $collection = BookingRequestCollection::create(
            new BookingRequest(
                new RequestId($rawBookings[0]['request_id']),
                new CheckInDate($rawBookings[0]['check_in']),
                new Nights($rawBookings[0]['nights']),
                new SellingRate($rawBookings[0]['selling_rate']),
                new Margin($rawBookings[0]['margin'])
            ),
            new BookingRequest(
                new RequestId($rawBookings[1]['request_id']),
                new CheckInDate($rawBookings[1]['check_in']),
                new Nights($rawBookings[1]['nights']),
                new SellingRate($rawBookings[1]['selling_rate']),
                new Margin($rawBookings[1]['margin'])
            ),
        );
        $this->creator
            ->expects($this->once())
            ->method('createCollection')
            ->with($rawBookings)
            ->willReturn($collection);

        $response = $this->useCase->execute($rawBookings);

        $responseData = $response->toArray();
        $this->assertEquals(6.25, $responseData['average']);
        $this->assertEquals(5.0, $responseData['minimum']);
        $this->assertEquals(7.5, $responseData['maximum']);
    }

    public function test_calculate_stats_for_empty_bookings(): void
    {
        $collection = BookingRequestCollection::create();

        $this->creator
            ->expects($this->once())
            ->method('createCollection')
            ->with([])
            ->willReturn($collection);

        $response = $this->useCase->execute([]);

        $responseData = $response->toArray();
        $this->assertEquals(0, $responseData['average']);
        $this->assertEquals(0, $responseData['minimum']);
        $this->assertEquals(0, $responseData['maximum']);
    }

    public function test_booking_with_zero_nights(): void
    {
        $rawBookings = [
            [
                'request_id' => '1',
                'check_in' => '2024-01-01',
                'nights' => 0,
                'selling_rate' => 100.0,
                'margin' => 10.0
            ]
        ];

        $collection = $this->createMock(BookingRequestCollection::class);
        $collection->method('map')->willReturn([0.0]);

        $this->creator
            ->expects($this->once())
            ->method('createCollection')
            ->with($rawBookings)
            ->willReturn($collection);

        $response = $this->useCase->execute($rawBookings);

        $responseData = $response->toArray();
        $this->assertEquals(0, $responseData['average']);
        $this->assertEquals(0, $responseData['minimum']);
        $this->assertEquals(0, $responseData['maximum']);
    }
}
