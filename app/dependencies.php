<?php

declare(strict_types=1);

use DI\ContainerBuilder;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Monolog\Processor\UidProcessor;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;

return function (ContainerBuilder $containerBuilder) {
    $containerBuilder->addDefinitions([
        LoggerInterface::class => function (ContainerInterface $c) {
            $logger = new Logger($c->get('logger.name'));
            $processor = new UidProcessor();
            $logger->pushProcessor($processor);
            $logger->pushHandler(new StreamHandler('mylog.log'));
            return $logger;
        },
    ]);
};
