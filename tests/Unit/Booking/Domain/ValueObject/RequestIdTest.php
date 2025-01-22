<?php

declare(strict_types=1);

namespace Tests\Unit\Booking\Domain\ValueObject;

use App\Booking\Domain\Exception\InvalidRequestIdException;
use App\Booking\Domain\ValueObject\RequestId;
use PHPUnit\Framework\TestCase;

class RequestIdTest extends TestCase
{
    public function test_create_valid_request_id(): void
    {
        $requestId = new RequestId('ACME');
        $this->assertEquals('ACME', $requestId->value());
    }

    public function test_cannot_create_with_zero_request_id(): void
    {
        $this->expectException(InvalidRequestIdException::class);
        new RequestId('');
    }

}
