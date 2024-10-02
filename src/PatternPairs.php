<?php declare(strict_types=1);

namespace EasyIni;

use EasyIni\Ini\Entry;

final class PatternPairs
{
    private array $lookups = [];
    private array $replacements = [];

    public function entry(string $key, Entry $entry): self
    {
        $value = $entry->getValue();
        // * For the sake of extensions
        if (!\is_array($value)) {
            $value = [$value];
        }
        foreach ($value as $v) {
            $this->basicEntry(
                $key,
                $v,
                $entry->getPrevValue(),
                $entry->toComment()
            );
        }
        return $this;
    }

    public function basicEntry(
        string $key,
        string|int $value = '\2',
        string|int $prevValue = '.*',
        bool $comment = false,
    ): self {
        $spacing = $key === 'extension' || $key === 'zend_extension' ? '' : ' ';
        return $this->set(
            \sprintf('~;?(%s) *= *(%s(?: *;.+)?)~', $key, $prevValue),
            comment($comment) . "\\1$spacing=$spacing$value"
        );
    }

    public function set(string $lookup, string $replacement): self
    {
        if ($lookup !== '') {
            Logger::debug(Lang::get('debug_pattern', $lookup, $replacement));
            $this->lookups[] = $lookup;
            $this->replacements[] = $replacement;
        }
        return $this;
    }

    public function getLookups(): array
    {
        return $this->lookups;
    }
    public function getReplacements(): array
    {
        return $this->replacements;
    }
}
