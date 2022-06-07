<?php

declare(strict_types=1);

use App\Application\Settings\Slim\Middleware\SessionMiddleware;
use Middlewares\Whoops;
use Slim\App;

return function (App $app) {
    $app->add(Whoops::class);
    $app->add(SessionMiddleware::class);
};
