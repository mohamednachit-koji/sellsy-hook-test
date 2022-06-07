<?php

declare(strict_types=1);

use App\Application\Settings\Slim\Handlers\HttpErrorHandler;
use App\Application\Settings\Slim\Handlers\ShutdownHandler;
use App\Application\Settings\Slim\ResponseEmitter\ResponseEmitter;
use Slim\Factory\ServerRequestCreatorFactory;

require __DIR__ . '/../vendor/autoload.php';

// TODO Sentry + Datadog
Sentry\init(['dsn' => 'https://3bc7fc7dfa204c3d823d798d438c2457@o1085005.ingest.sentry.io/6300921']);

$bootstrap = require __DIR__ . '/../app/bootstrap.php';
$app = $bootstrap();

// Create Request object from globals
$serverRequestCreator = ServerRequestCreatorFactory::create();
$request = $serverRequestCreator->createServerRequestFromGlobals();

// Create Error Handler
$callableResolver = $app->getCallableResolver();
$responseFactory = $app->getResponseFactory();
$errorHandler = new HttpErrorHandler($callableResolver, $responseFactory);

$displayErrorDetails = $app->getContainer()->get('slim.displayErrorDetails');

// Create Shutdown Handler
$shutdownHandler = new ShutdownHandler($request, $errorHandler, $displayErrorDetails);
register_shutdown_function($shutdownHandler);

// Add Routing Middleware
$app->addRoutingMiddleware();

// Add Body Parsing Middleware
$app->addBodyParsingMiddleware();

// Add Error Middleware
$logError = $app->getContainer()->get('slim.logError');
$logErrorDetails = $app->getContainer()->get('slim.logErrorDetails');
$errorMiddleware = $app->addErrorMiddleware($displayErrorDetails, $logError, $logErrorDetails);
$errorMiddleware->setDefaultErrorHandler($errorHandler);

// Run App & Emit Response
$response = $app->handle($request);
$responseEmitter = new ResponseEmitter();
$responseEmitter->emit($response);
