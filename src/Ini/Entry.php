<?php declare(strict_types=1);

namespace EasyIni\Ini;

use EasyIni\Lang;
use function EasyIni\validateSnake;

use Attribute;

#[Attribute(Attribute::TARGET_PROPERTY)]
final class Entry
{
    public function __construct(private ?string $name = null)
    {
        if ($name === '') {
            throw new \InvalidArgumentException(Lang::get('err_entry_empty'));
        }
        if ($name !== null && !validateSnake($name)) {
            throw new \InvalidArgumentException(Lang::get('err_entry_snake'));
        }
    }

    public function getName(): ?string
    {
        return $this->name;
    }
}
