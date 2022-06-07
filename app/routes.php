<?php

declare(strict_types=1);

use App\Application\Http\Customer\SellsyEventAction;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\App;
use Slim\Interfaces\RouteCollectorProxyInterface as Group;

return function (App $app) {
    $app->options('/{routes:.*}', function (Request $request, Response $response) {
        // CORS Pre-Flight OPTIONS Request Handler
        return $response;
    });

    $app->get('/', function (Request $request, Response $response) {
        return $response->withStatus(200);
    });

    $app->group('/customers', function (Group $group) {
        $group->post('/sellsy-event', SellsyEventAction::class);
    });
};
