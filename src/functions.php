<?php declare(strict_types=1);

namespace EasyIni;

function prefixArray(string $prefix, array $array)
{
    return \preg_filter('~^~', $prefix, $array);
}

function filterArray(array $arr): array
{
    return \array_unique(\array_filter($arr));
}

function digitCount(int $number): int
{
    return $number !== 0 ? (int)(\log10(\abs($number)) + 1) : 1;
}

function path(string ...$parts): string
{
    return \implode(\DIRECTORY_SEPARATOR, $parts);
}

function validateBytes(string|int $bytes): bool
{
    if (\is_int($bytes)) {
        return $bytes >= 0;
    }
    return (bool)\preg_match('/^\d+[KMG]?$/i', $bytes);
}

function validateSnake(string $text): bool
{
    return (bool)\preg_match('/^(?<word>[a-z][a-z\d]*)(?:_(?&word))*$/', $text);
}

function camelToSnake(string $text): string
{
    return \strtolower(\preg_replace('/(?<!^)[A-Z]/', '_$0', $text));
}

function classShortName(string|object $class): string
{
    if (\is_string($class) && !\class_exists($class)) {
        throw new \InvalidArgumentException(Lang::get('err_class_resolve', $class));
    }
    $refl = new \ReflectionClass($class);
    return $refl->getShortName();
}

function comment(bool $condition = true): string
{
    return $condition ? ';' : '';
}
