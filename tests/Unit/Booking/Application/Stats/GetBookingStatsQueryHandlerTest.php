<?php

namespace Tests\Unit\Booking\Application\Stats;

use App\Booking\Application\Stats\GetBookingStatsQueryHandler;
use App\Booking\Application\Stats\GetBookingStatsUseCase;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class GetBookingStatsQueryHandlerTest extends TestCase
{
    private GetBookingStatsUseCase|MockObject $useCase;
    private GetBookingStatsQueryHandler       $handler;

    protected function setUp(): void
    {
        $this->useCase = $this->createMock(GetBookingStatsUseCase::class);
        $this->handler = new GetBookingStatsQueryHandler($this->useCase);
    }

    public function test_invoke_executes_use_case(): void
    {
        $query = GetBookingStatsQueryMother::withTwoBookings();
        $expectedResponse = GetBookingStatsResponseMother::create();

        $this->useCase
            ->expects($this->once())
            ->method('execute')
            ->with($query->getBookings())
            ->willReturn($expectedResponse);

        $response = $this->handler->__invoke($query);
        $this->assertSame($expectedResponse, $response);
    }
}