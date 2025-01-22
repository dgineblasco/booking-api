<?php

declare(strict_types=1);

namespace Tests\Unit\Booking\Application\Creator;

use App\Booking\Application\Creator\BookingRequestCreator;
use App\Booking\Domain\BookingRequestCollection;
use App\Booking\Domain\BookingRequestFactory;
use App\Booking\Domain\Exception\DuplicateBookingIdException;
use App\Booking\Domain\Exception\InvalidBookingInputException;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class BookingRequestCreatorTest extends TestCase
{
    private BookingRequestFactory|MockObject $factory;

    private BookingRequestCreator $creator;

    protected function setUp(): void
    {
        $this->factory = $this->createMock(BookingRequestFactory::class);
        $this->creator = new BookingRequestCreator($this->factory);
    }

    public function test_throws_exception_for_non_array_input(): void
    {
        $this->expectException(InvalidBookingInputException::class);

        $invalidInput = [1, 1, 1];

        $this->creator->createCollection($invalidInput);
    }

    public function test_throws_exception_for_mixed_array_input(): void
    {
        $this->expectException(InvalidBookingInputException::class);

        $mixedInput = [
            ['request_id' => 'A', 'check_in' => '2024-01-01'],
            null,
            1,
            'string',
            true,
            new \stdClass()
        ];

        $this->creator->createCollection($mixedInput);
    }

    public function test_throws_exception_for_duplicate_ids(): void
    {
        $this->expectException(DuplicateBookingIdException::class);

        $duplicateIds = [
            [
                'request_id' => 'same_id',
                'check_in' => '2024-01-01',
                'nights' => 2,
                'selling_rate' => 100,
                'margin' => 10
            ],
            [
                'request_id' => 'same_id',
                'check_in' => '2024-01-03',
                'nights' => 3,
                'selling_rate' => 150,
                'margin' => 15
            ]
        ];

        $this->creator->createCollection($duplicateIds);
    }

    public function test_handles_empty_input_array(): void
    {
        $collection = $this->creator->createCollection([]);

        $this->assertInstanceOf(BookingRequestCollection::class, $collection);
        $this->assertTrue($collection->isEmpty());
    }


    public function test_throws_exception_for_special_characters_in_ids(): void
    {
        $specialCharData = [
            [
                'request_id' => 'ID@#$%^',
                'check_in' => '2024-01-01',
                'nights' => 2,
                'selling_rate' => 100,
                'margin' => 10
            ]
        ];

        $this->factory
            ->expects($this->once())
            ->method('createFromArray')
            ->with($specialCharData[0])
            ->willThrowException(new InvalidBookingInputException('Invalid ID format'));

        $this->expectException(InvalidBookingInputException::class);
        $this->creator->createCollection($specialCharData);
    }

    public function test_throws_exception_for_numeric_request_ids(): void
    {
        $numericIds = [
            [
                'request_id' => 123,
                'check_in' => '2024-01-01',
                'nights' => 2,
                'selling_rate' => 100,
                'margin' => 10
            ]
        ];

        $this->factory
            ->expects($this->once())
            ->method('createFromArray')
            ->with($numericIds[0])
            ->willThrowException(new InvalidBookingInputException('ID must be string'));

        $this->expectException(InvalidBookingInputException::class);
        $this->creator->createCollection($numericIds);
    }

    public function test_throws_exception_for_whitespace_only_ids(): void
    {
        $whitespaceIds = [
            [
                'request_id' => '   ',
                'check_in' => '2024-01-01',
                'nights' => 2,
                'selling_rate' => 100,
                'margin' => 10
            ]
        ];

        $this->factory
            ->expects($this->once())
            ->method('createFromArray')
            ->with($whitespaceIds[0])
            ->willThrowException(new InvalidBookingInputException('ID cannot be empty'));

        $this->expectException(InvalidBookingInputException::class);
        $this->creator->createCollection($whitespaceIds);
    }

    public function test_throws_exception_for_malformed_json_structure(): void
    {
        $this->expectException(InvalidBookingInputException::class);

        $malformedData = [
            [
                "request_id" => ["nested" => "array"],
                "check_in" => "2024-01-01"
            ]
        ];

        $this->factory
            ->expects($this->once())
            ->method('createFromArray')
            ->with($malformedData[0])
            ->willThrowException(new InvalidBookingInputException('Invalid request_id format'));

        $this->creator->createCollection($malformedData);
    }

    public function test_throws_exception_for_empty_array_elements(): void
    {
        $this->expectException(InvalidBookingInputException::class);

        $emptyArrayElements = [
            [],
            []
        ];

        $this->creator->createCollection($emptyArrayElements);
    }

    public function test_throws_exception_for_nested_arrays(): void
    {
        $this->expectException(InvalidBookingInputException::class);

        $nestedArrays = [
            [
                [
                    'request_id' => 'A',
                    'check_in' => '2024-01-01'
                ]
            ]
        ];

        $this->creator->createCollection($nestedArrays);
    }
}
