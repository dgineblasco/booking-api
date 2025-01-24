<?php

declare(strict_types=1);

namespace App\Booking\Domain;

use App\Shared\Domain\Collection\Collection;

class BookingRequestCollection extends Collection
{
    private array $requestIds = [];
    private float $totalProfit = 0.0;
    private float $averageNight = 0.0;
    private float $minNight = 0.0;
    private float $maxNight = 0.0;

    public static function create(BookingRequest ...$bookingRequests): self
    {
        return new self($bookingRequests);
    }

    public static function fromArray(array $bookingRequests): self
    {
        return new self(array_map(
            static fn (BookingRequest $request): BookingRequest => $request,
            $bookingRequests
        ));
    }

    protected function type(): string
    {
        return BookingRequest::class;
    }

    public function sortedByCheckIn(): self
    {
        $items = $this->items;
        usort($items, static fn (BookingRequest $a, BookingRequest $b) => $a->getCheckIn() <=> $b->getCheckIn());
        return new self($items);
    }

    public function calculateMetrics(): void
    {
        $this->requestIds = [];
        $this->totalProfit = 0.0;
        $profitsPerNight = [];

        foreach ($this->items as $booking) {
            $this->requestIds[] = $booking->getId();
            $this->totalProfit += $booking->getTotalProfit();
            $profitsPerNight[] = $booking->getProfitPerNight();
        }

        if (!empty($profitsPerNight)) {
            $this->averageNight = array_sum($profitsPerNight) / count($profitsPerNight);
            $this->minNight = min($profitsPerNight);
            $this->maxNight = max($profitsPerNight);
        }
    }

    public function getRequestIds(): array {
        return $this->requestIds;
    }

    public function getTotalProfit(): float {
        return $this->totalProfit;
    }

    public function getAverageNight(): float {
        return $this->averageNight;
    }

    public function getMinNight(): float {
        return $this->minNight;
    }

    public function getMaxNight(): float {
        return $this->maxNight;
    }
}
