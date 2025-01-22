<?php

declare(strict_types=1);

namespace App\Booking\Domain;

use App\Booking\Domain\ValueObject\CheckInDate;
use App\Booking\Domain\ValueObject\Margin;
use App\Booking\Domain\ValueObject\Nights;
use App\Booking\Domain\ValueObject\RequestId;
use App\Booking\Domain\ValueObject\SellingRate;

class BookingRequestFactory
{
    private const array REQUIRED_FIELDS = [
        'request_id',
        'check_in',
        'nights',
        'selling_rate',
        'margin'
    ];

    public function createFromArray(array $data): BookingRequest
    {
        $this->validateArrayStructure($data);

        return new BookingRequest(
            new RequestId($data['request_id']),
            new CheckInDate($data['check_in']),
            new Nights($data['nights']),
            new SellingRate($data['selling_rate']),
            new Margin($data['margin'])
        );
    }

    private function validateArrayStructure(array $data): void
    {
        $requiredFields = self::REQUIRED_FIELDS;
        $missingFields = array_diff($requiredFields, array_keys($data));

        if (!empty($missingFields)) {
            throw new \InvalidArgumentException(
                'Error! Fields ' . implode(', ', $missingFields)." are mandatory"
            );
        }
    }
}
