<?php

declare(strict_types=1);

namespace App\Booking\Application\Creator;

use App\Booking\Domain\BookingRequestCollection;
use App\Booking\Domain\BookingRequestFactory;
use App\Booking\Domain\Exception\DuplicateBookingIdException;
use App\Booking\Domain\Exception\InvalidBookingInputException;

class BookingRequestCreator
{
    public function __construct(
        private readonly BookingRequestFactory $factory
    ) {
    }

    public function createCollection(array $rawBookings): BookingRequestCollection
    {
        return BookingRequestCollection::fromArray(
            $this->validateAndCreateRequests($rawBookings)
        );
    }

    private function validateAndCreateRequests(array $rawBookings): array
    {
        $bookingRequests = $requestIds = [];

        foreach ($rawBookings as $index => $booking) {
            if (!is_array($booking) || empty($booking) || !isset($booking['request_id'])) {
                throw new InvalidBookingInputException(
                    sprintf('Invalid booking at index %d: Must be a non-empty array with request_id', $index)
                );
            }

            if (in_array($booking['request_id'], $requestIds, true)) {
                throw new DuplicateBookingIdException(
                    sprintf('Duplicate booking with request_id %s.', $booking['request_id'])
                );
            }
            $requestIds[] = $booking['request_id'];

            $bookingRequests[] = $this->factory->createFromArray($booking);
        }

        return $bookingRequests;
    }
}
