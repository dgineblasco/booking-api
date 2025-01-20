<?php

namespace App\Booking\Application\Maximize;

use App\Booking\Domain\BookingRequest;
use App\Booking\Domain\BookingRequestFactory;

class MaximizeBookingUseCase
{
    public function __construct(
        private readonly BookingRequestFactory $factory
    ) {}

    public function execute(array $rawBookings): MaximizeBookingResponse
    {
        $bookings = $this->createBookingRequests($rawBookings);

        usort($bookings, fn(BookingRequest $a, BookingRequest $b) => $a->getCheckIn() <=> $b->getCheckIn());

        $bestCombination = $this->findBestBookingCombination($bookings);

        return $this->buildResponse($bestCombination);
    }

    private function createBookingRequests(array $rawBookings): array
    {
        return array_map(
            fn(array $booking) => $this->factory->createFromArray($booking),
            $rawBookings
        );
    }

    /** @param BookingRequest[] $bookings */
    private function findBestBookingCombination(array $bookings): array
    {
        $bestCombination = [];
        $maxProfit = 0;
        $totalBookings = count($bookings);

        foreach ($bookings as $startIndex => $firstBooking) {
            $currentCombination = [$firstBooking];
            $currentProfit = $firstBooking->getTotalProfit();
            $lastBooking = $firstBooking;

            for ($nextIndex = $startIndex + 1; $nextIndex < $totalBookings; $nextIndex++) {
                $nextBooking = $bookings[$nextIndex];

                if ($this->isBookingCompatible($lastBooking, $nextBooking)) {
                    $currentCombination[] = $nextBooking;
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

    private function buildResponse(array $bestCombination): MaximizeBookingResponse
    {
        $profitsPerNight = array_map(
            fn(BookingRequest $booking) => $booking->getProfitPerNight(),
            $bestCombination
        );

        return new MaximizeBookingResponse(
            $this->extractBookingIds($bestCombination),
            $this->calculateTotalProfit($bestCombination),
            $this->calculateAverageProfit($profitsPerNight),
            min($profitsPerNight),
            max($profitsPerNight)
        );
    }

    private function extractBookingIds(array $bookings): array
    {
        return array_map(
            fn(BookingRequest $booking) => $booking->getId(),
            $bookings
        );
    }

    private function calculateTotalProfit(array $bookings): float
    {
        return array_sum(array_map(
            fn(BookingRequest $booking) => $booking->getTotalProfit(),
            $bookings
        ));
    }

    private function calculateAverageProfit(array $profits): float
    {
        return empty($profits) ? 0 : array_sum($profits) / count($profits);
    }
}
