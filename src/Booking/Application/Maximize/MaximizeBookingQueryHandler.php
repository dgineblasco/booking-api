<?php

declare(strict_types=1);

namespace App\Booking\Application\Maximize;

class MaximizeBookingQueryHandler
{
    public function __construct(
        private readonly MaximizeBookingUseCase $useCase
    ) {
    }

    public function __invoke(MaximizeBookingQuery $query): MaximizeBookingResponse
    {
        return $this->useCase->execute($query->getBookings());
    }
}
