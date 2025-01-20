<?php

namespace App\Booking\Domain;

use DateTimeImmutable;

class BookingRequestFactory
{
    public function createFromArray(array $data): BookingRequest
    {
        $this->validateArrayStructure($data);

        return new BookingRequest(
            $data['request_id'],
            new DateTimeImmutable($data['check_in']),
            intval($data['nights']),
            floatval($data['selling_rate']),
            floatval($data['margin']),
        );
    }

    private function validateArrayStructure(array $data): void
    {
        $requiredFields = ['request_id', 'check_in', 'nights', 'selling_rate', 'margin'];
        $missingFields = array_diff($requiredFields, array_keys($data));

        if (!empty($missingFields)) {
            throw new \InvalidArgumentException(
                'Error! Fields ' . implode(', ', $missingFields)." are mandatory"
            );
        }
    }
}
