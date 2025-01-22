<?php

declare(strict_types=1);

namespace App\Booking\Application\Stats;

use App\Shared\Application\Response\Response;

final readonly class GetBookingStatsResponse implements Response
{
    public function __construct(
        private float $average,
        private float $minimum,
        private float $maximum
    ) {
    }

    public function toArray(): array
    {
        return [
            'average' => $this->average,
            'minimum' => $this->minimum,
            'maximum' => $this->maximum
        ];
    }
}
