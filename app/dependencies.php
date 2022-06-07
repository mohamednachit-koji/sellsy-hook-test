<?php

declare(strict_types=1);

use DI\ContainerBuilder;
use Monolog\Handler\ErrorLogHandler;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Monolog\Processor\UidProcessor;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;
use Sentry\Monolog\Handler as SentryHandler;
use Sentry\State\HubInterface;

return function (ContainerBuilder $containerBuilder) {
    $containerBuilder->addDefinitions([
        LoggerInterface::class => function (ContainerInterface $c) {
            $logger = new Logger($c->get('logger.name'));
            $processor = new UidProcessor();
            $logger->pushProcessor($processor);
            $handler = match ($c->get('logger.type')) {
                'stream' => new StreamHandler($c->get('logger.path'), $c->get('logger.level')),
                // https://github.com/PHP-DI/PHP-DI/issues/787
                // Not working yet 'error_log' => new ErrorLogHandler(level: $c->get('logger.level')),
                'error_log' => new ErrorLogHandler(
                    ErrorLogHandler::OPERATING_SYSTEM,
                    $c->get('logger.level'),
                ),
                'sentry' => new SentryHandler($c->get(HubInterface::class), $c->get('logger.level')),
                default => throw new \InvalidArgumentException(
                    'Unsupported logger type: ' . $c->get('logger.type'),
                ),
            };
            $logger->pushHandler($handler);
            return $logger;
        },
    ]);
};
