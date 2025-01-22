<?php

declare(strict_types=1);

namespace App\Booking\Application\Maximize;

use App\Shared\Application\Response\Response;

final readonly class MaximizeBookingResponse implements Response
{
    public function __construct(
        private array $requestIds,
        private float $totalProfit,
        private float $avgNight,
        private float $minNight,
        private float $maxNight
    ) {
    }

    public function toArray(): array
    {
        return [
            'request_ids'   => $this->requestIds,
            'total_profit'  => $this->totalProfit,
            'avg_night'     => $this->avgNight,
            'min_night'     => $this->minNight,
            'max_night'     => $this->maxNight
        ];
    }
}
