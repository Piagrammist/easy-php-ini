<?php declare(strict_types=1);

namespace EasyIni\Ini;

use function EasyIni\classShortName;

enum EntryState implements \JsonSerializable
{
    case COMMENT;
    case UNCOMMENT;
    case UNTOUCHED;

    public function jsonSerialize(): string
    {
        return classShortName(self::class) . '::' . $this->name;
    }
}
