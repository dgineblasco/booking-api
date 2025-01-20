<?php

use App\Booking\UI\Controller\UI\Controller\MaximizeController;
use App\Booking\UI\Controller\UI\Controller\StatsController;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

$routes = new RouteCollection();

$routes->add('stats', new Route(
    path: '/stats',
    defaults: ['_controller' => [StatsController::class, '__invoke']],
    methods: ['POST']
));
$routes->add('maximize', new Route(
    path: '/maximize',
    defaults: ['_controller' => [MaximizeController::class, '__invoke']],
    methods: ['POST']
));

return $routes;