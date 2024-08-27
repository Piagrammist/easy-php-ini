<?php declare(strict_types=1);

namespace EasyIni;

final class PatternPairs
{
    private array $lookups = [];
    private array $replacements = [];

    public function entry(
        string $key,
        string $value = '\2',
        string $prevValue = '.+',
        bool $comment = false,
    ): self {
        $spacing = $key === 'extension' || $key === 'zend_extension' ? '' : ' ';
        return $this->set(
            sprintf('~;?(%s) *= *%s~', $key, $prevValue ? "($prevValue)" : ''),
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
