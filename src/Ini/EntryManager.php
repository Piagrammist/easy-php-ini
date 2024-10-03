<?php declare(strict_types=1);

namespace EasyIni\Ini;

use Generator;
use JsonSerializable;

use ReflectionClass;
use ReflectionProperty;
use ReflectionAttribute;

use EasyIni\Lang;
use function EasyIni\camelToSnake;

abstract class EntryManager implements JsonSerializable
{
    private ?string $namespace = null;

    /** Instantiates all props with the `Entry` attribute. */
    public function __construct()
    {
        /**
         * @var ReflectionAttribute $attr
         * @var ReflectionProperty  $property
         */
        foreach ($this->iterEntriesInternal() as $attr => $property) {
            $entry = $attr->newInstance();
            if (!$entry->getName()) {
                $entry->setName(camelToSnake($property->getName()));
            }
            if ($this->namespace) {
                $entry->setNamespace($this->namespace);
            }
            $this->{$property->name} = $entry;
        }
    }

    /**
     * Sets the entries' prefix/namespace.
     * ! Must be called before `__construct()`
     */
    protected function setNamespace(string $namespace): void
    {
        if ($namespace === '') {
            throw new \InvalidArgumentException(Lang::get('err_namespace_empty'));
        }
        $this->namespace = $namespace;
    }

    protected function setEntry(
        Entry &$prop,
        mixed $value = null,
        EntryState $state = EntryState::UNCOMMENT,
        ValueFormat $format = ValueFormat::NONE,
    ): static {
        if ($value !== null) {
            $prop->setValue($value, $format);
        }
        $prop->setState($state);
        return $this;
    }

    /** @return Generator<ReflectionAttribute, ReflectionProperty> */
    protected function iterEntriesInternal(): Generator
    {
        $refl = new ReflectionClass($this);
        foreach ($refl->getProperties() as $property) {
            $attr = $property->getAttributes(Entry::class)[0] ?? false;
            if ($attr) {
                yield $attr => $property;
            }
        }
    }

    /** @return Generator<string, Entry> */
    public function iterEntries(): Generator
    {
        foreach ($this->iterEntriesInternal() as $property) {
            /** @var Entry $value */
            $value = $property->getValue($this);
            yield $value->getName() => $value;
        }
    }

    /** @return array<string, Entry> */
    public function getEntries(): array
    {
        return \iterator_to_array($this->iterEntries());
    }

    public function jsonSerialize(): array
    {
        return $this->getEntries();
    }
}
