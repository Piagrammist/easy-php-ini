<?php declare(strict_types=1);

namespace EasyIni;

final class Lang
{
    private static array $strings = [
        'error_bytes' => '%s size must be a positive value in bytes, ' .
            "or with standard PHP data size suffixes (K, M or G) e.g. '256M'",
    ];

    public static function get(string $key, string ...$args): string
    {
        if (!isset(self::$strings[$key])) {
            throw new \InvalidArgumentException("Invalid string key provided '$key'");
        }
        return sprintf(self::$strings[$key], ...$args);
    }
}
