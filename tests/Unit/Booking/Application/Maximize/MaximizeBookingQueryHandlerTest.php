<?php

namespace Tests\Unit\Booking\Application\Maximize;

use App\Booking\Application\Maximize\MaximizeBookingQueryHandler;
use App\Booking\Application\Maximize\MaximizeBookingUseCase;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class MaximizeBookingQueryHandlerTest extends TestCase
{
    private MaximizeBookingUseCase|MockObject $useCase;
    private MaximizeBookingQueryHandler       $handler;

    protected function setUp(): void
    {
        $this->useCase = $this->createMock(MaximizeBookingUseCase::class);
        $this->handler = new MaximizeBookingQueryHandler($this->useCase);
    }

    public function test_invoke_executes_use_case(): void
    {
        $query = MaximizeBookingQueryMother::withTwoBookings();
        $expectedResponse = MaximizeBookingResponseMother::create();

        $this->useCase
            ->expects($this->once())
            ->method('execute')
            ->with($query->getBookings())
            ->willReturn($expectedResponse);

        $response = $this->handler->__invoke($query);
        $this->assertSame($expectedResponse, $response);
    }
}