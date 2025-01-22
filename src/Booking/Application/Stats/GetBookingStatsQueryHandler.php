<?php

declare(strict_types=1);

namespace App\Booking\Application\Stats;

class GetBookingStatsQueryHandler
{
    public function __construct(
        private readonly GetBookingStatsUseCase $useCase
    ) {
    }

    public function __invoke(GetBookingStatsQuery $query): GetBookingStatsResponse
    {
        return $this->useCase->execute($query->getBookings());
    }
}
