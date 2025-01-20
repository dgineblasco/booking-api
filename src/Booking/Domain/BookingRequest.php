<?php

namespace App\Booking\Domain;

use DateTimeImmutable;

class BookingRequest
{
    public function __construct(
        private readonly string            $id,
        private readonly DateTimeImmutable $checkIn,
        private readonly int               $nights,
        private readonly float             $sellingRate,
        private readonly float             $margin,

    ) {
        $this->validate();
    }

    private function validate(): void
    {
        if ($this->nights <= 0) {
            throw new \InvalidArgumentException('Nights must be positive');
        }
        if ($this->sellingRate <= 0) {
            throw new \InvalidArgumentException('Selling rate must be positive');
        }
        if ($this->margin <= 0 || $this->margin > 100) {
            throw new \InvalidArgumentException('Margin must be between 0 and 100');
        }
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getCheckIn(): DateTimeImmutable
    {
        return $this->checkIn;
    }

    public function getCheckOut(): DateTimeImmutable
    {
        return $this->checkIn->modify("+{$this->nights} days");
    }

    public function getProfitPerNight(): float
    {
        return ($this->sellingRate * ($this->margin / 100)) / $this->nights;
    }

    public function getTotalProfit(): float
    {
        return $this->sellingRate * ($this->margin / 100);
    }

    public function getNights(): int
    {
        return $this->nights;
    }

    public function getSellingRate(): float
    {
        return $this->sellingRate;
    }

    public function getMargin(): float
    {
        return $this->margin;
    }

}