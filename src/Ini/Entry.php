<?php declare(strict_types=1);

namespace EasyIni\Ini;

use function EasyIni\validateSnake;

use Attribute;

#[Attribute(Attribute::TARGET_PROPERTY)]
final class Entry
{
    public function __construct(private ?string $name = null)
    {
        if ($name === '') {
            throw new \InvalidArgumentException('Entry name cannot be empty');
        }
        if ($name !== null && !validateSnake($name)) {
            throw new \InvalidArgumentException('Entry name must be snake_case');
        }
    }

    public function getName(): ?string
    {
        return $this->name;
    }
}
