<?php

declare(strict_types=1);

namespace Tests\Unit\Booking\UI\Controller;

use App\Booking\Application\Maximize\MaximizeBookingQueryHandler;
use App\Booking\UI\Controller\UI\Controller\MaximizeController;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Tests\Unit\Booking\Application\Maximize\MaximizeBookingQueryMother;
use Tests\Unit\Booking\Application\Maximize\MaximizeBookingResponseMother;

class MaximizeControllerTest extends TestCase
{
    private MaximizeBookingQueryHandler|MockObject $queryHandler;
    private MaximizeController $controller;

    protected function setUp(): void
    {
        $this->queryHandler = $this->createMock(MaximizeBookingQueryHandler::class);
        $this->controller = new MaximizeController($this->queryHandler);
    }
    public function test_invoke_returns_maximize_for_valid_request(): void
    {
        $bookings = [
            [
                'request_id' => '1',
                'check_in' => '2026-01-01',
                'nights' => 2,
                'selling_rate' => 100.0,
                'margin' => 10.0
            ]
        ];

        $request = new Request(
            content: json_encode($bookings)
        );

        $query = MaximizeBookingQueryMother::create($bookings);
        $response = MaximizeBookingResponseMother::create();

        $this->queryHandler
            ->expects($this->once())
            ->method('__invoke')
            ->with($query)
            ->willReturn($response);

        $jsonResponse = $this->controller->__invoke($request);

        $this->assertEquals(200, $jsonResponse->getStatusCode());
        $this->assertEquals($response->toArray(), json_decode($jsonResponse->getContent(), true));
    }

    public function test_invoke_returns_400_for_empty_request(): void
    {
        $request = new Request(content: json_encode([]));

        $jsonResponse = $this->controller->__invoke($request);

        $this->assertEquals(400, $jsonResponse->getStatusCode());
        $this->assertEquals(
            ['error' => 'Request content required'],
            json_decode($jsonResponse->getContent(), true)
        );
    }

    public function test_invoke_returns_500_for_unexpected_error(): void
    {
        $request = new Request(content: json_encode(['some' => 'data']));

        $this->queryHandler
            ->method('__invoke')
            ->willThrowException(new \RuntimeException('Unexpected error'));

        $jsonResponse = $this->controller->__invoke($request);

        $this->assertEquals(500, $jsonResponse->getStatusCode());
        $this->assertEquals(
            ['error' => 'Internal server error'],
            json_decode($jsonResponse->getContent(), true)
        );
    }
}
