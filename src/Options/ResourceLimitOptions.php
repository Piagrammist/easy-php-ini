<?php declare(strict_types=1);

namespace EasyIni\Options;

use EasyIni\Logger;
use EasyIni\ErrorCounter;

use EasyIni\Ini\Entry;
use EasyIni\Ini\Entries;
use EasyIni\Ini\EntryState;

use function EasyIni\validateBytes;

final class ResourceLimitOptions
{
    use Entries;

    #[Entry]
    private $maxInputTime = EntryState::UNTOUCHED;

    #[Entry]
    private $maxInputVars = EntryState::UNTOUCHED;

    #[Entry]
    private $maxExecutionTime = EntryState::UNTOUCHED;

    #[Entry]
    private $maxInputNestingLevel = EntryState::UNTOUCHED;

    #[Entry]
    private $maxMultipartBodyParts = EntryState::UNTOUCHED;

    #[Entry]
    private $memoryLimit = EntryState::UNTOUCHED;

    public function setMaxInputTime(EntryState|int $maxInputTime): self
    {
        $this->maxInputTime = $maxInputTime;
        return $this;
    }

    public function setMaxInputVars(EntryState|int $maxInputVars): self
    {
        $this->maxInputVars = $maxInputVars;
        return $this;
    }

    public function setMaxExecutionTime(EntryState|int $maxExecutionTime): self
    {
        $this->maxExecutionTime = $maxExecutionTime;
        return $this;
    }

    public function setMaxInputNestingLevel(EntryState|int $maxInputNestingLevel): self
    {
        $this->maxInputNestingLevel = $maxInputNestingLevel;
        return $this;
    }

    public function setMaxMultipartBodyParts(EntryState|int $maxMultipartBodyParts): self
    {
        $this->maxMultipartBodyParts = $maxMultipartBodyParts;
        return $this;
    }

    public function setMemoryLimit(EntryState|string|int $memoryLimit): self
    {
        if (!(is_bool($memoryLimit) || validateBytes($memoryLimit))) {
            Logger::error('Memory limit must be a positive value in bytes, ' .
                "or with standard PHP data size suffixes (K, M or G) e.g. '256M'");
            ErrorCounter::increment();
        }
        $this->memoryLimit = $memoryLimit;
        return $this;
    }
}
