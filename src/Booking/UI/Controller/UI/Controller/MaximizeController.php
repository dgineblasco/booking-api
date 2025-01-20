<?php
namespace App\Booking\UI\Controller\UI\Controller;

use App\Booking\Application\Maximize\MaximizeBookingQuery;
use App\Booking\Application\Maximize\MaximizeBookingQueryHandler;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

final readonly class MaximizeController
{
    public function __construct(
        private MaximizeBookingQueryHandler $queryHandler
    ) {}

    public function __invoke(Request $request): JsonResponse
    {
        try {
            $data = json_decode($request->getContent(), true);

            if (empty($data)) {
                throw new \InvalidArgumentException('Request content required');
            }

            $query = new MaximizeBookingQuery($data);
            $stats = $this->queryHandler->__invoke($query);

            return new JsonResponse($stats->toArray());

        } catch (\InvalidArgumentException $e) {
            return new JsonResponse(['error' => $e->getMessage()], 400);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => 'Internal server error'], 500);
        }
    }
}