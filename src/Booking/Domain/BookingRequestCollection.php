<?php

declare(strict_types=1);

namespace App\Booking\Domain;

use App\Shared\Domain\Collection\Collection;

class BookingRequestCollection extends Collection
{
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

    public function remove(string $bookingRequestId): self
    {
        return new self(array_filter(
            $this->items,
            static fn (BookingRequest $request): bool => $request->getId() !== $bookingRequestId
        ));
    }

    public function getTotalProfit(): float
    {
        return array_reduce(
            $this->items,
            static fn (float $carry, BookingRequest $item): float => $carry + $item->getTotalProfit(),
            0.0
        );
    }

    public function sortedByCheckIn(): self
    {
        $items = $this->items;
        usort($items, static fn (BookingRequest $a, BookingRequest $b) => $a->getCheckIn() <=> $b->getCheckIn());

        return new self($items);
    }
}
