<?php declare(strict_types=1);

namespace EasyIni;

class PatternPairs
{
    protected array $lookups = [];
    protected array $replacements = [];

    public function set(string $index, string $lookup, string $replacement): static
    {
        if (!($lookup === '' || $index === '' || isset($this->lookups[$index]))) {
            Logger::debug("Pattern($index)");
            $this->lookups[$index] = $lookup;
            $this->replacements[$index] = $replacement;
        }
        return $this;
    }

    public function get(string $key): ?array
    {
        return match ($key) {
            'lookups'      => $this->lookups,
            'replacements' => $this->replacements,
            default        => null,
        };
    }
}
