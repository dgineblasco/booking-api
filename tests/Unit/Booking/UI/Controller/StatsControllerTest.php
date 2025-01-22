<?php

namespace Tests\Unit\Booking\UI\Controller;

use App\Booking\Application\Stats\GetBookingStatsQueryHandler;
use App\Booking\UI\Controller\UI\Controller\StatsController;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Tests\Unit\Booking\Application\Stats\GetBookingStatsQueryMother;
use Tests\Unit\Booking\Application\Stats\GetBookingStatsResponseMother;

class StatsControllerTest extends TestCase
{
    private GetBookingStatsQueryHandler|MockObject $queryHandler;
    private StatsController $controller;

    protected function setUp(): void
    {
        $this->queryHandler = $this->createMock(GetBookingStatsQueryHandler::class);
        $this->controller = new StatsController($this->queryHandler);
    }
    public function test_invoke_returns_stats_for_valid_request(): void
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

        $query = GetBookingStatsQueryMother::create($bookings);
        $response = GetBookingStatsResponseMother::create();

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