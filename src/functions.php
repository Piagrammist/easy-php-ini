<?php declare(strict_types=1);

namespace EasyIni;

function array_prefix(string $prefix, array $array)
{
    return preg_filter('~^~', $prefix, $array);
}

function digitCount(int $number): int
{
    return $number !== 0 ? (int)(log10(abs($number)) + 1) : 1;
}

function path(string ...$parts): string
{
    return implode(DIRECTORY_SEPARATOR, $parts);
}

function validateBytes(string|int $digit): bool
{
    return (bool)preg_match('/^\d+[KMG]?$/i', (string)$digit);
}

function camelToSnake(string $text): string
{
    return strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $text));
}

function classShortName(string|object $class): string
{
    if (is_string($class) && !class_exists($class)) {
        throw new \InvalidArgumentException("Could not resolve class '$class'");
    }
    $refl = new \ReflectionClass($class);
    return $refl->getShortName();
}

function pluralSuffix(int $count, string $suffix = 's'): string
{
    return $count > 1 ? $suffix : '';
}

function comment(bool $condition = true): string
{
    return $condition ? ';' : '';
}
