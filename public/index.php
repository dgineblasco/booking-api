<?php
require_once __DIR__ . '/../vendor/autoload.php';

use App\Bootstrap;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\HttpFoundation\JsonResponse;

$container = Bootstrap::createContainer();
$routes = require __DIR__ . '/../config/routes.php';

$context = new RequestContext();
$request = Request::createFromGlobals();
$context->fromRequest($request);

$matcher = new UrlMatcher($routes, $context);

try {
    $parameters = $matcher->match($request->getPathInfo());

    $controllerClass = $parameters['_controller'][0];
    $method = $parameters['_controller'][1];

    $controller = $container->get($controllerClass);
    $response = $controller->$method($request);
    $response->send();
} catch (\Exception $e) {
    $response = new JsonResponse(['error' => $e->getMessage()], 404);
    $response->send();
}