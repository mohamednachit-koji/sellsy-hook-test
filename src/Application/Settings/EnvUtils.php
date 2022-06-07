<?php

declare(strict_types=1);

namespace App\Application\Settings;

final class EnvUtils
{
    private const KEY = 'APP_ENV';

    private static function getEnv(): string
    {
        $value = getenv(self::KEY);
        if ($value !== false) {
            return $value;
        }
        return $_ENV[self::KEY] ?? 'prod';
    }

    public static function isProduction(): bool
    {
        return self::getEnv() === 'prod';
    }
}
