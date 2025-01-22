<?php

declare(strict_types=1);

namespace Tests\Functional\Booking\UI\Controller;

use GuzzleHttp\Client;
use PHPUnit\Framework\TestCase;

class MaximizeControllerTest extends TestCase
{
    private Client $client;

    protected function setUp(): void
    {
        $this->client = new Client([
            'base_uri' => 'http://localhost:8089',
            'http_errors' => false
        ]);
    }

    public function test_maximize_finds_best_combination(): void
    {
        $bookings = [
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

        $response = $this->client->post('/maximize', [
            'body' => json_encode($bookings),
            'headers' => ['Content-Type' => 'application/json']
        ]);

        $responseData = json_decode($response->getBody()->getContents(), true);

        $this->assertEquals(['A', 'C'], $responseData['request_ids']);
        $this->assertEquals(140, $responseData['total_profit']);
        $this->assertEquals(14, $responseData['avg_night']);
        $this->assertEquals(8, $responseData['min_night']);
        $this->assertEquals(20, $responseData['max_night']);
    }
}
