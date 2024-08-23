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
            sprintf('~;?(%s)\s*=\s*(%s)~', $key, $prevValue),
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

    public function get(string $key): ?array
    {
        return match ($key) {
            'lookups'      => $this->lookups,
            'replacements' => $this->replacements,
            default        => null,
        };
    }
}
