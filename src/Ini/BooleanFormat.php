<?php declare(strict_types=1);

namespace EasyIni\Ini;

enum BooleanFormat
{
    case BINARY;
    case SWITCH;

    public function get(bool $truth): string
    {
        return self::fmt($this, $truth);
    }

    public static function fmt(self $mode, bool $truth): string
    {
        return match ($mode) {
            self::BINARY => $truth ? '1' : '0',
            self::SWITCH => $truth ? 'On' : 'Off',
        };
    }
}

