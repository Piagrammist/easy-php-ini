<?php declare(strict_types=1);

namespace EasyIni;

use EasyIni\Ini\EntryState;
use EasyIni\Ini\EntryValue;

final class PatternPairs
{
    private array $lookups = [];
    private array $replacements = [];

    public function entry(
        string $key,
        ?EntryValue $value = null,
        string|int $prevValue = '.*',
    ): self {
        $value ??= new EntryValue(state: EntryState::UNCOMMENT);
        return $this->basicEntry($key, $value->getValue(), $prevValue, $value->toComment());
    }

    public function basicEntry(
        string $key,
        string|int $value = '\2',
        string|int $prevValue = '.*',
        bool $comment = false,
    ): self {
        $spacing = $key === 'extension' || $key === 'zend_extension' ? '' : ' ';
        return $this->set(
            sprintf('~;?(%s) *= *(%s)~', $key, $prevValue),
            comment($comment) . "\\1$spacing=$spacing$value"
        );
    }

    public function set(string $lookup, string $replacement): self
    {
        if ($lookup !== '') {
            Logger::debug("PatternPair{ '$lookup' => '$replacement' }");
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
