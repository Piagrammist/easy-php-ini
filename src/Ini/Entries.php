<?php declare(strict_types=1);

namespace EasyIni\Ini;

use Generator;
use ReflectionClass;

use EasyIni\Logger;
use function EasyIni\toSnakeCase;

trait Entries
{
    public function iterEntries(): Generator
    {
        $refl = new ReflectionClass($this);
        foreach ($refl->getProperties() as $property) {
            $value = $property->getValue($this);
            $attr = $property->getAttributes(Entry::class)[0] ?? null;
            if ($attr !== null && $value !== null) {
                $name = $attr->newInstance()->getName() ?? toSnakeCase($property->getName());
                Logger::debug("Entry{ '$name' = '$value' }");
                yield $name => $value;
            }
        }
    }

    public function getEntries(): array
    {
        return iterator_to_array($this->iterEntries());
    }
}
