<?php

declare(strict_types=1);

namespace Tests\Functional\Booking\UI\Controller;

use GuzzleHttp\Client;
use PHPUnit\Framework\TestCase;

class StatsControllerTest extends TestCase
{
    private Client $client;

    protected function setUp(): void
    {
        $this->client = new Client([
            'base_uri' => 'http://localhost:8089',
            'http_errors' => false
        ]);
    }

    public function test_stats_calculate_profit_stats(): void
    {
        $bookings = [
            [
                'request_id' => '1',
                'check_in' => '2026-01-01',
                'nights' => 2,
                'selling_rate' => 100.0,
                'margin' => 10.5
            ]
        ];

        $response = $this->client->post('/stats', [
            'body' => json_encode($bookings),
            'headers' => ['Content-Type' => 'application/json']
        ]);

        $responseData = json_decode($response->getBody()->getContents(), true);

        $this->assertEquals(5.25, $responseData['average']);
        $this->assertEquals(5.25, $responseData['minimum']);
        $this->assertEquals(5.25, $responseData['maximum']);
    }
}
