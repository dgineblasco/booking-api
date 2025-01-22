<?php

declare(strict_types=1);

namespace App\Booking\Domain;

use App\Booking\Domain\ValueObject\CheckInDate;
use App\Booking\Domain\ValueObject\Margin;
use App\Booking\Domain\ValueObject\Nights;
use App\Booking\Domain\ValueObject\RequestId;
use App\Booking\Domain\ValueObject\SellingRate;
use DateTimeImmutable;

class BookingRequest
{
    public function __construct(
        private readonly RequestId   $id,
        private readonly CheckInDate $checkIn,
        private readonly Nights      $nights,
        private readonly SellingRate $sellingRate,
        private readonly Margin      $margin,
    ) {
    }

    public function getId(): string
    {
        return $this->id->value();
    }

    public function getCheckIn(): DateTimeImmutable
    {
        return $this->checkIn->value();
    }

    public function getCheckOut(): DateTimeImmutable
    {
        return $this->getCheckIn()->modify("+{$this->getNights()} days");
    }

    public function getProfitPerNight(): float
    {
        return ($this->getSellingRate() * ($this->getMargin() / 100)) / $this->getNights();
    }

    public function getTotalProfit(): float
    {
        return $this->getSellingRate() * ($this->getMargin() / 100);
    }

    public function getNights(): int
    {
        return $this->nights->value();
    }

    public function getSellingRate(): float
    {
        return $this->sellingRate->value();
    }

    public function getMargin(): float
    {
        return $this->margin->value();
    }

}
