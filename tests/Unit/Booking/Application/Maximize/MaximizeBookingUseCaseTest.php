<?php

namespace Tests\Unit\Booking\Application\Maximize;

use App\Booking\Application\Maximize\MaximizeBookingUseCase;
use App\Booking\Domain\BookingRequestFactory;
use PHPUnit\Framework\TestCase;

class MaximizeBookingUseCaseTest extends TestCase
{
    private BookingRequestFactory $factory;
    private MaximizeBookingUseCase $useCase;

    protected function setUp(): void
    {
        $this->factory = new BookingRequestFactory();
        $this->useCase = new MaximizeBookingUseCase($this->factory);
    }

    public function test_execute_finds_best_combination(): void
    {
        $rawBookings = [
            [
                'request_id' => 'A',
                'check_in' => '2024-01-01',
                'nights' => 5,
                'selling_rate' => 1000,
                'margin' => 10
            ],
            [
                'request_id' => 'B',
                'check_in' => '2024-01-03',
                'nights' => 5,
                'selling_rate' => 700,
                'margin' => 10
            ],
            [
                'request_id' => 'C',
                'check_in' => '2024-01-07',
                'nights' => 5,
                'selling_rate' => 400,
                'margin' => 10
            ],
            [
                'request_id' => 'D',
                'check_in' => '2024-01-04',
                'nights' => 3,
                'selling_rate' => 400,
                'margin' => 10
            ]
        ];

        $response = $this->useCase->execute($rawBookings);
        $responseArray = $response->toArray();

        $this->assertEquals(['A', 'C'], $responseArray['request_ids']);
        $this->assertEquals(140.0, $responseArray['total_profit']);
    }

    public function test_execute_with_single_booking(): void
    {
        $rawBookings = [[
            'request_id' => 'A',
            'check_in' => '2024-01-01',
            'nights' => 5,
            'selling_rate' => 1000,
            'margin' => 10
        ]];

        $response = $this->useCase->execute($rawBookings);
        $responseArray = $response->toArray();

        $this->assertEquals(['A'], $responseArray['request_ids']);
        $this->assertEquals(100.0, $responseArray['total_profit']);
    }
}