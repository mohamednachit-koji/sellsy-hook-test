<?php

declare(strict_types=1);

use App\Application\Settings\EnvUtils;
use DI\ContainerBuilder;
use Monolog\Logger;

return function (ContainerBuilder $containerBuilder) {
    $containerBuilder->addDefinitions([
        'slim.displayErrorDetails' => !EnvUtils::isProduction(),
        'slim.logError' => false,
        'slim.logErrorDetails' => false,
    ], [
        'logger.name' => DI\env('APP_NAME', 'brs-api-lieu'),
        'logger.level' => DI\env('LOG_LEVEL', Logger::DEBUG),
        'logger.type' => DI\env('LOG_TYPE', 'stream'),
        'logger.path' => isset($_ENV['docker']) ? 'php://stdout' : __DIR__ . '/../var/logs/app.log',
    ], [
        'sellsy.api.url' => 'https://apifeed.sellsy.com/0/',
        'sellsy.user.token' => DI\env('SELLSY_USER_TOKEN'),
        'sellsy.user.secret' => DI\env('SELLSY_USER_SECRET'),
        'sellsy.consumer.key' => DI\env('SELLSY_CONSUMER_KEY'),
        'sellsy.consumer.secret' => DI\env('SELLSY_CONSUMER_SECRET'),
    ], [
        'odoo.url' => DI\env('ODOO_URL'),
        'odoo.database' => DI\env('ODOO_DATABASE'),
        'odoo.username' => DI\env('ODOO_USERNAME'),
        'odoo.password' => DI\env('ODOO_PASSWORD'),
    ], [
        'doctrine.metadata_dirs' => [__DIR__ . '/../src'],
        'doctrine.dev_mode' => true, //!EnvUtils::isProduction(),
        'doctrine.connection' => [
            'driver' => 'pdo_mysql',
            'host' => DI\env('DB_HOST'),
            'port' => DI\env('DB_PORT', 3306),
            'user' => DI\env('DB_USER'),
            'password' => DI\env('DB_PASSWORD'),
            'dbname' => DI\env('DB_NAME'),
            'charset' => DI\env('DB_CHARSET', 'UTF8'),
        ],
    ], [
        'api.user.url' => DI\env('API_USER_URL'),
        'api.user.clientId' => DI\env('API_USER_CLIENT_ID'),
        'api.user.clientSecret' => DI\env('API_USER_CLIENT_SECRET'),
    ]);
};
