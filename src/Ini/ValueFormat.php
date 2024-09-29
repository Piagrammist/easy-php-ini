<?php declare(strict_types=1);

namespace EasyIni\Ini;

enum ValueFormat
{
    case ARR_CSV;
    case ARR_REGEX;
    case BOOL_BINARY;
    case BOOL_SWITCH;
    case NONE;

    public function get(mixed $value): mixed
    {
        return self::fmt($this, $value);
    }

    public static function fmt(self $mode, mixed $value): mixed
    {
        return match ($mode) {
            self::ARR_CSV     => \implode(',', $value),
            self::ARR_REGEX   => \implode('|', $value),
            self::BOOL_BINARY => $value ? '1' : '0',
            self::BOOL_SWITCH => $value ? 'On' : 'Off',
            default           => $value,
        };
    }
}

