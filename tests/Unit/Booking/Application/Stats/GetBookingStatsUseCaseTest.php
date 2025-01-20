<?php

namespace Tests\Unit\Booking\Application\Stats;

use App\Booking\Application\Stats\GetBookingStatsResponse;
use App\Booking\Application\Stats\GetBookingStatsUseCase;
use App\Booking\Domain\BookingRequest;
use App\Booking\Domain\BookingRequestFactory;
use PHPUnit\Framework\TestCase;

class GetBookingStatsUseCaseTest extends TestCase
{
    private BookingRequestFactory  $factory;
    private GetBookingStatsUseCase $useCase;

    protected function setUp(): void
    {
        $this->factory = $this->createMock(BookingRequestFactory::class);
        $this->useCase = new GetBookingStatsUseCase($this->factory);
    }

    public function test_execute_calculate_stats(): void
    {
        $bookingData = [
            [
                'request_id' => 'request_test_1',
                'check_in' => '2024-01-01',
                'nights' => 2,
                'selling_rate' => 100.0,
                'margin' => 10.0
            ],
            [
                'request_id' => 'request_test_2',
                'check_in' => '2024-01-03',
                'nights' => 3,
                'selling_rate' => 150.0,
                'margin' => 15.0
            ]
        ];

        $booking1 = $this->createMock(BookingRequest::class);
        $booking1->method('getProfitPerNight')->willReturn(5.0);

        $booking2 = $this->createMock(BookingRequest::class);
        $booking2->method('getProfitPerNight')->willReturn(7.5);

        $this->factory->expects($this->exactly(2))
            ->method('createFromArray')
            ->willReturnOnConsecutiveCalls($booking1, $booking2);

        $response = $this->useCase->execute($bookingData);

        $this->assertInstanceOf(GetBookingStatsResponse::class, $response);
        $responseArray = $response->toArray();
        $this->assertEquals(6.25, $responseArray['average']);
        $this->assertEquals(5.0, $responseArray['minimum']);
        $this->assertEquals(7.5, $responseArray['maximum']);
    }
}

