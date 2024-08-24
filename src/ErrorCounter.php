<?php declare(strict_types=1);

namespace EasyIni;

final class ErrorCounter
{
    private static int $counter = 0;

    public static function empty(): bool
    {
        return self::$counter === 0;
    }

    public static function count(): int
    {
        return self::$counter;
    }

    public static function increment(): void
    {
        ++self::$counter;
    }

    /*
    public static function decrement(): void
    {
        if (self::$counter === 0) {
            return;
        }
        --self::$counter;
    }
    */
}
