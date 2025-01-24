<?php

declare(strict_types=1);

namespace App\Booking\Application\Maximize;

use App\Booking\Application\Creator\BookingRequestCreator;
use App\Booking\Domain\BookingRequest;
use App\Booking\Domain\BookingRequestCollection;

class MaximizeBookingUseCase
{
    public function __construct(
        private readonly BookingRequestCreator $bookingRequestCreator
    ) {
    }

    public function execute(array $rawBookings): MaximizeBookingResponse
    {
        $bookings = $this->bookingRequestCreator->createCollection($rawBookings);

        $bestCombination = $this->findBestBookingCombination($bookings->sortedByCheckIn());

        return $this->buildResponse($bestCombination);
    }

    private function findBestBookingCombination(BookingRequestCollection $bookings): BookingRequestCollection
    {
        $bestCombination = BookingRequestCollection::create();
        $maxProfit = 0;
        $bookingsArray = $bookings->toArray();
        $totalBookings = $bookings->count();

        foreach ($bookingsArray as $startIndex => $firstBooking) {
            $currentCombination = BookingRequestCollection::create($firstBooking);
            $currentProfit = $firstBooking->getTotalProfit();
            $lastBooking = $firstBooking;

            for ($nextIndex = $startIndex + 1; $nextIndex < $totalBookings; $nextIndex++) {
                $nextBooking = $bookingsArray[$nextIndex];

                if ($this->isBookingCompatible($lastBooking, $nextBooking)) {
                    $currentCombination = $currentCombination->add($nextBooking);
                    $currentProfit += $nextBooking->getTotalProfit();
                    $lastBooking = $nextBooking;
                }
            }

            if ($currentProfit > $maxProfit) {
                $maxProfit = $currentProfit;
                $bestCombination = $currentCombination;
            }
        }

        return $bestCombination;
    }

    private function isBookingCompatible(BookingRequest $lastBooking, BookingRequest $nextBooking): bool
    {
        return $nextBooking->getCheckIn() >= $lastBooking->getCheckOut();
    }

    private function buildResponse(BookingRequestCollection $bestCombination): MaximizeBookingResponse
    {
        $bestCombination->calculateMetrics();

        return new MaximizeBookingResponse(
            $bestCombination->getRequestIds(),
            $bestCombination->getTotalProfit(),
            $bestCombination->getAverageNight(),
            $bestCombination->getMinNight(),
            $bestCombination->getMaxNight()
        );
    }
}
