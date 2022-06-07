<?php

declare(strict_types=1);

use App\Application\Settings\EnvUtils;
use DI\ContainerBuilder;
use Slim\Factory\AppFactory;

return function () {

    $dotenv = require __DIR__ . '/dotenv.php';
    $dotenv();

    // Instantiate PHP-DI ContainerBuilder
    $containerBuilder = new ContainerBuilder();

    if (EnvUtils::isProduction()) {
        $containerBuilder->enableCompilation(__DIR__ . '/../var/cache');
    }

    // Set up settings
    $settings = require __DIR__ . '/settings.php';
    $settings($containerBuilder);

    // Set up dependencies
    $dependencies = require __DIR__ . '/dependencies.php';
    $dependencies($containerBuilder);

    // Set up repositories
    $repositories = require __DIR__ . '/repositories.php';
    $repositories($containerBuilder);

    // Build PHP-DI Container instance
    $container = $containerBuilder->build();

    // Instantiate the app
    AppFactory::setContainer($container);
    $app = AppFactory::create();

    // Register middleware
    $middleware = require __DIR__ . '/middleware.php';
    $middleware($app);

    // Register routes
    $routes = require __DIR__ . '/routes.php';
    $routes($app);

    return $app;
};
