<?php declare(strict_types=1);

namespace EasyIni\Ini;

use Attribute;

#[Attribute(Attribute::TARGET_PROPERTY)]
class Entry
{
    public function __construct(private ?string $name = null)
    {
        if ($name === '') {
            throw new \InvalidArgumentException('Entry name cannot be empty');
        }
    }

    public function getName(): ?string
    {
        return $this->name;
    }
}
