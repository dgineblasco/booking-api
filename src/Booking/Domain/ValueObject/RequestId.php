<?php

namespace App\Booking\Domain\ValueObject;

use App\Booking\Domain\Exception\InvalidRequestIdException;

final readonly class RequestId
{
    private string $value;

    public function __construct(string $value)
    {
        $value = trim($value);

        if (empty($value)) {
            throw new InvalidRequestIdException('Request ID cannot be empty');
        }
        $this->value = $value;
    }

    public function value(): string
    {
        return $this->value;
    }
}