<?php

declare(strict_types=1);

namespace Tests\Unit\Booking\Application\Maximize;

use App\Booking\Application\Creator\BookingRequestCreator;
use App\Booking\Application\Maximize\MaximizeBookingUseCase;
use App\Booking\Domain\BookingRequest;
use App\Booking\Domain\BookingRequestCollection;
use DateTimeImmutable;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class MaximizeBookingUseCaseTest extends TestCase
{
    private BookingRequestCreator|MockObject $creator;
    private MaximizeBookingUseCase           $useCase;

    protected function setUp(): void
    {
        $this->creator = $this->createMock(BookingRequestCreator::class);
        $this->useCase = new MaximizeBookingUseCase($this->creator);
    }

    public function test_find_best_booking_combination(): void
    {
        $rawBookings = [
            [
                'request_id' => 'A',
                'check_in' => '2026-01-01',
                'nights' => 5,
                'selling_rate' => 1000,
                'margin' => 10
            ],
            [
                'request_id' => 'B',
                'check_in' => '2026-01-03',
                'nights' => 5,
                'selling_rate' => 700,
                'margin' => 10
            ],
            [
                'request_id' => 'C',
                'check_in' => '2026-01-07',
                'nights' => 5,
                'selling_rate' => 400,
                'margin' => 10
            ]
        ];

        $bookingA = $this->createBookingMock('A', '2026-01-01', '2026-01-06', 100.0, 20.0);
        $bookingB = $this->createBookingMock('B', '2026-01-03', '2026-01-08', 70.0, 14.0);
        $bookingC = $this->createBookingMock('C', '2026-01-07', '2026-01-12', 40.0, 8.0);

        $collection = $this->createMock(BookingRequestCollection::class);
        $collection->method('sortedByCheckIn')->willReturn($collection);
        $collection->method('toArray')->willReturn([$bookingA, $bookingB, $bookingC]);
        $collection->method('count')->willReturn(3);
        $collection->method('map')->willReturnCallback(function ($callback) use ($bookingA, $bookingC) {
            return array_map($callback, [$bookingA, $bookingC]);
        });
        $collection->method('getTotalProfit')->willReturn(140.0);

        $this->creator
            ->expects($this->once())
            ->method('createCollection')
            ->with($rawBookings)
            ->willReturn($collection);

        $response = $this->useCase->execute($rawBookings);

        $responseData = $response->toArray();
        $this->assertEquals(['A', 'C'], $responseData['request_ids']);
        $this->assertEquals(140.0, $responseData['total_profit']);
        $this->assertEquals(14.0, $responseData['avg_night']);
        $this->assertEquals(8.0, $responseData['min_night']);
        $this->assertEquals(20.0, $responseData['max_night']);
    }

    public function test_empty_bookings_returns_empty_response(): void
    {
        $emptyCollection = $this->createMock(BookingRequestCollection::class);
        $emptyCollection->method('sortedByCheckIn')->willReturn($emptyCollection);
        $emptyCollection->method('toArray')->willReturn([]);
        $emptyCollection->method('map')->willReturn([]);
        $emptyCollection->method('getTotalProfit')->willReturn(0.0);

        $this->creator
            ->expects($this->once())
            ->method('createCollection')
            ->with([])
            ->willReturn($emptyCollection);

        $response = $this->useCase->execute([]);

        $responseData = $response->toArray();
        $this->assertEquals([], $responseData['request_ids']);
        $this->assertEquals(0, $responseData['total_profit']);
        $this->assertEquals(0, $responseData['avg_night']);
        $this->assertEquals(0, $responseData['min_night']);
        $this->assertEquals(0, $responseData['max_night']);
    }

    private function createBookingMock(
        string $id,
        string $checkIn,
        string $checkOut,
        float $totalProfit,
        float $profitPerNight
    ): BookingRequest|MockObject {
        $booking = $this->createMock(BookingRequest::class);

        $booking->method('getId')->willReturn($id);
        $booking->method('getCheckIn')->willReturn(new DateTimeImmutable($checkIn));
        $booking->method('getCheckOut')->willReturn(new DateTimeImmutable($checkOut));
        $booking->method('getTotalProfit')->willReturn($totalProfit);
        $booking->method('getProfitPerNight')->willReturn($profitPerNight);

        return $booking;
    }

    public function test_single_booking_returns_itself(): void
    {
        $rawBookings = [
            [
                'request_id' => 'A',
                'check_in' => '2026-01-01',
                'nights' => 5,
                'selling_rate' => 1000,
                'margin' => 10
            ]
        ];

        $booking = $this->createBookingMock('A', '2026-01-01', '2026-01-06', 100.0, 20.0);
        $collection = $this->createCollectionMock([$booking]);

        $this->creator->method('createCollection')->willReturn($collection);

        $response = $this->useCase->execute($rawBookings);
        $responseData = $response->toArray();

        $this->assertEquals(['A'], $responseData['request_ids']);
        $this->assertEquals(100.0, $responseData['total_profit']);
    }

    public function test_overlapping_bookings_chooses_most_profitable(): void
    {
        $rawBookings = [
            [
                'request_id' => 'A',
                'check_in' => '2026-01-01',
                'nights' => 5,
                'selling_rate' => 1000,
                'margin' => 10
            ],
            [
                'request_id' => 'B',
                'check_in' => '2026-01-02',
                'nights' => 2,
                'selling_rate' => 2000,
                'margin' => 10
            ],
            [
                'request_id' => 'C',
                'check_in' => '2026-01-03',
                'nights' => 1,
                'selling_rate' => 500,
                'margin' => 10
            ]
        ];

        $bookingA = $this->createBookingMock('A', '2026-01-01', '2026-01-06', 100.0, 20.0);
        $bookingB = $this->createBookingMock('B', '2026-01-02', '2026-01-04', 200.0, 100.0);
        $bookingC = $this->createBookingMock('C', '2026-01-03', '2026-01-04', 50.0, 50.0);

        $collection = $this->createCollectionMock([$bookingA, $bookingB, $bookingC]);
        $this->creator->method('createCollection')->willReturn($collection);

        $response = $this->useCase->execute($rawBookings);
        $responseData = $response->toArray();

        $this->assertEquals(['B'], $responseData['request_ids']);
    }

    public function test_consecutive_bookings_same_dates(): void
    {
        $rawBookings = [
            [
                'request_id' => 'A',
                'check_in' => '2026-01-01',
                'nights' => 3,
                'selling_rate' => 1000,
                'margin' => 10
            ],
            [
                'request_id' => 'B',
                'check_in' => '2026-01-04',
                'nights' => 3,
                'selling_rate' => 1000,
                'margin' => 10
            ]
        ];

        $bookingA = $this->createBookingMock('A', '2026-01-01', '2026-01-04', 100.0, 33.33);
        $bookingB = $this->createBookingMock('B', '2026-01-04', '2026-01-07', 100.0, 33.33);

        $collection = $this->createCollectionMock([$bookingA, $bookingB]);
        $this->creator->method('createCollection')->willReturn($collection);

        $response = $this->useCase->execute($rawBookings);
        $responseData = $response->toArray();

        $this->assertEquals(['A', 'B'], $responseData['request_ids']);
    }

    public function test_bookings_with_extreme_profits(): void
    {
        $rawBookings = [
            [
                'request_id' => 'A',
                'check_in' => '2026-01-01',
                'nights' => 1,
                'selling_rate' => 999999.99,
                'margin' => 100
            ],
            [
                'request_id' => 'B',
                'check_in' => '2026-01-03',
                'nights' => 1,
                'selling_rate' => 0.01,
                'margin' => 1
            ]
        ];

        $bookingA = $this->createBookingMock('A', '2026-01-01', '2026-01-02', 999999.99, 999999.99);
        $bookingB = $this->createBookingMock('B', '2026-01-03', '2026-01-04', 0.0001, 0.0001);

        $collection = $this->createCollectionMock([$bookingA, $bookingB]);
        $this->creator->method('createCollection')->willReturn($collection);

        $response = $this->useCase->execute($rawBookings);
        $responseData = $response->toArray();

        $this->assertContains('A', $responseData['request_ids']);
        $this->assertTrue($responseData['max_night'] > 999999);
        $this->assertTrue($responseData['min_night'] < 0.001);
    }

    public function test_bookings_far_in_future(): void
    {
        $rawBookings = [
            [
                'request_id' => 'A',
                'check_in' => '2525-12-31',
                'nights' => 1,
                'selling_rate' => 100,
                'margin' => 10
            ]
        ];

        $bookingA = $this->createBookingMock('A', '2525-12-31', '2526-01-01', 10.0, 10.0);

        $collection = $this->createCollectionMock([$bookingA]);
        $this->creator->method('createCollection')->willReturn($collection);

        $response = $this->useCase->execute($rawBookings);
        $responseData = $response->toArray();

        $this->assertEquals(['A'], $responseData['request_ids']);
    }

    private function createCollectionMock(array $bookings): BookingRequestCollection|MockObject
    {
        $collection = $this->createMock(BookingRequestCollection::class);
        $collection->method('sortedByCheckIn')->willReturn($collection);
        $collection->method('toArray')->willReturn($bookings);
        $collection->method('count')->willReturn(count($bookings));
        $collection->method('map')->willReturnCallback(function ($callback) use ($bookings) {
            return array_map($callback, $bookings);
        });
        $collection->method('getTotalProfit')->willReturn(
            array_sum(array_map(fn ($b) => $b->getTotalProfit(), $bookings))
        );

        return $collection;
    }
}
