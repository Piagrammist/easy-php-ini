<?php declare(strict_types=1);

namespace EasyIni;

class CommonOptions
{
    use IniProps;

    private int|bool|null $maxInputTime = null;
    private int|bool|null $maxInputVars = null;
    private int|bool|null $maxExecutionTime = null;
    private int|bool|null $maxInputNestingLevel = null;
    private int|bool|null $maxMultipartBodyParts = null;
    private string|int|bool|null $memoryLimit = null;

    public function setMaxInputTime(int|bool $maxInputTime): static
    {
        $this->maxInputTime = $maxInputTime;
        return $this;
    }
    public function getMaxInputTime()
    {
        return $this->maxInputTime;
    }

    public function setMaxInputVars(int|bool $maxInputVars): static
    {
        $this->maxInputVars = $maxInputVars;
        return $this;
    }
    public function getMaxInputVars()
    {
        return $this->maxInputVars;
    }

    public function setMaxExecutionTime(int|bool $maxExecutionTime): static
    {
        $this->maxExecutionTime = $maxExecutionTime;
        return $this;
    }
    public function getMaxExecutionTime()
    {
        return $this->maxExecutionTime;
    }

    public function setMaxInputNestingLevel(int|bool $maxInputNestingLevel): static
    {
        $this->maxInputNestingLevel = $maxInputNestingLevel;
        return $this;
    }
    public function getMaxInputNestingLevel()
    {
        return $this->maxInputNestingLevel;
    }

    public function setMaxMultipartBodyParts(int|bool $maxMultipartBodyParts): static
    {
        $this->maxMultipartBodyParts = $maxMultipartBodyParts;
        return $this;
    }
    public function getMaxMultipartBodyParts()
    {
        return $this->maxMultipartBodyParts;
    }

    public function setMemoryLimit(string|int|bool $memoryLimit): static
    {
        if (!(is_bool($memoryLimit) || validateBytes($memoryLimit))) {
            throw new \InvalidArgumentException('Memory limit must be a positive value in bytes, ' .
                "or with standard PHP data size suffixes (K, M or G) e.g. '256M'");
        }
        $this->memoryLimit = $memoryLimit;
        return $this;
    }
    public function getMemoryLimit()
    {
        return $this->memoryLimit;
    }
}
