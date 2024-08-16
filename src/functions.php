<?php declare(strict_types=1);

namespace EasyIni;

function array_prefix(string $prefix, array $array)
{
    return preg_filter('~^~', $prefix, $array);
}

function digitCount(int $number): int
{
    return $number !== 0 ? (int)(log10($number) + 1) : 1;
}

function path(string ...$parts): string
{
    return implode(DIRECTORY_SEPARATOR, $parts);
}
