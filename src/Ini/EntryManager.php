<?php declare(strict_types=1);

namespace EasyIni\Ini;

use Generator;
use ReflectionClass;

use EasyIni\Logger;
use function EasyIni\camelToSnake;

abstract class EntryManager implements \JsonSerializable
{
    public function __construct()
    {
        foreach ($this->iterRawEntryNames() as $name) {
            $this->{$name} = new EntryValue;
        }
    }

    protected function setEntry(
        EntryValue &$prop,
        mixed $value,
        EntryState $state,
        ?callable $validator = null,
    ): self {
        if ($value !== null) {
            $validator && $validator($value);
            $prop->setValue($value);
        }
        $prop->setState($state);
        return $this;
    }

    /** @return Generator<string> */
    private function iterRawEntryNames(): Generator
    {
        $refl = new ReflectionClass($this);
        foreach ($refl->getProperties() as $property) {
            $attr = $property->getAttributes(Entry::class)[0] ?? false;
            if ($attr) {
                yield $property->getName();
            }
        }
    }

    /** @return Generator<string, EntryValue> */
    public function iterEntries(): Generator
    {
        $refl = new ReflectionClass($this);
        foreach ($refl->getProperties() as $property) {
            $value = $property->getValue($this);
            $attr = $property->getAttributes(Entry::class)[0] ?? false;
            if ($attr) {
                $name = $attr->newInstance()->getName()
                    ?: camelToSnake($property->getName());
                Logger::debug("Entry{ '$name' = " . json_encode($value, JSON_UNESCAPED_SLASHES) . " }");
                yield $name => $value;
            }
        }
    }

    /** @return array<string, EntryValue> */
    public function getEntries(): array
    {
        return iterator_to_array($this->iterEntries());
    }

    public function jsonSerialize(): array
    {
        return $this->getEntries();
    }
}
