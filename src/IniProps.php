<?php declare(strict_types=1);

namespace EasyIni;

trait IniProps
{
    public function getProps(): array
    {
        $result = [];
        $refl = new \ReflectionClass($this);
        foreach ($refl->getProperties() as $property) {
            $result[toSnakeCase($property->getName())] = $property->getValue($this);
        }
        return $result;
    }
}
