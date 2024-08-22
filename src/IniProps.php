<?php declare(strict_types=1);

namespace EasyIni;

trait IniProps
{
    public function getProps(): array
    {
        $result = [];
        $refl = new \ReflectionClass($this);
        foreach ($refl->getProperties() as $property) {
            $value = $property->getValue($this);
            if ($value !== null) {
                $result[toSnakeCase($property->getName())] = $value;
            }
        }
        return $result;
    }
}
