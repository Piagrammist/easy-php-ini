<?php declare(strict_types=1);

namespace EasyIni\Ini;

use ReflectionClass;
use function EasyIni\toSnakeCase;

trait Entries
{
    public function getEntries(): array
    {
        $result = [];
        $refl = new ReflectionClass($this);
        foreach ($refl->getProperties() as $property) {
            $value = $property->getValue($this);
            $attr = $property->getAttributes(Entry::class)[0] ?? null;
            if ($attr !== null && $value !== null) {
                $name = $attr->newInstance()->getName() ?? toSnakeCase($property->getName());
                $result[$name] = $value;
            }
        }
        return $result;
    }
}
