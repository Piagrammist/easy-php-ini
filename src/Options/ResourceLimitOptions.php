<?php declare(strict_types=1);

namespace EasyIni\Options;

use EasyIni\Logger;
use EasyIni\Ini\Entry;
use EasyIni\Ini\Entries;
use EasyIni\ErrorCounter;

use function EasyIni\validateBytes;

final class ResourceLimitOptions
{
    use Entries;

    #[Entry]
    private $maxInputTime = null;

    #[Entry]
    private $maxInputVars = null;

    #[Entry]
    private $maxExecutionTime = null;

    #[Entry]
    private $maxInputNestingLevel = null;

    #[Entry]
    private $maxMultipartBodyParts = null;

    #[Entry]
    private $memoryLimit = null;

    public function setMaxInputTime(int|bool $maxInputTime): self
    {
        $this->maxInputTime = $maxInputTime;
        return $this;
    }

    public function setMaxInputVars(int|bool $maxInputVars): self
    {
        $this->maxInputVars = $maxInputVars;
        return $this;
    }

    public function setMaxExecutionTime(int|bool $maxExecutionTime): self
    {
        $this->maxExecutionTime = $maxExecutionTime;
        return $this;
    }

    public function setMaxInputNestingLevel(int|bool $maxInputNestingLevel): self
    {
        $this->maxInputNestingLevel = $maxInputNestingLevel;
        return $this;
    }

    public function setMaxMultipartBodyParts(int|bool $maxMultipartBodyParts): self
    {
        $this->maxMultipartBodyParts = $maxMultipartBodyParts;
        return $this;
    }

    public function setMemoryLimit(string|int|bool $memoryLimit): self
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
