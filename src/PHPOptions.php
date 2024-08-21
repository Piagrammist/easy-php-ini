<?php declare(strict_types=1);

namespace EasyIni;

class PHPOptions
{

    public function __construct(
        private int      $maxExecutionTime = 30,
        private int      $maxInputTime = 60,
        private int|false $maxInputNestingLevel = false,
        private int|false $maxInputVars = false,
        private int|false $maxMultipartBodyParts = false,
        private string   $memoryLimit = '128M',
    )
    {
        if (!validateDigits($memoryLimit)) {
            Logger::warning('Memory limit must be a positive value in bytes, ' .
                "or with standard PHP data size suffixes (K, M or G) e.g. '256M'");
            $this->memoryLimit = '128M';
        }
    }

    /**
     * @return int
     */
    public function getMaxExecutionTime(): int
    {
        return $this->maxExecutionTime;
    }

    /**
     * @param int $maxExecutionTime
     */
    public function setMaxExecutionTime(int $maxExecutionTime): static
    {
        $this->maxExecutionTime = $maxExecutionTime;
        return $this;
    }

    /**
     * @return int
     */
    public function getMaxInputTime(): int
    {
        return $this->maxInputTime;
    }

    /**
     * @param int $maxInputTime
     */
    public function setMaxInputTime(int $maxInputTime): static
    {
        $this->maxInputTime = $maxInputTime;
        return $this;
    }

    /**
     * @return bool|int
     */
    public function getMaxInputNestingLevel(): bool|int
    {
        return $this->maxInputNestingLevel;
    }

    /**
     * @param bool|int $maxInputNestingLevel
     */
    public function setMaxInputNestingLevel(bool|int $maxInputNestingLevel): static
    {
        $this->maxInputNestingLevel = $maxInputNestingLevel;
        return $this;
    }

    /**
     * @return bool|int
     */
    public function getMaxInputVars(): bool|int
    {
        return $this->maxInputVars;
    }

    /**
     * @param bool|int $maxInputVars
     */
    public function setMaxInputVars(bool|int $maxInputVars): static
    {
        $this->maxInputVars = $maxInputVars;
        return $this;
    }

    /**
     * @return bool|int
     */
    public function getMaxMultipartBodyParts(): bool|int
    {
        return $this->maxMultipartBodyParts;
    }

    /**
     * @param bool|int $maxMultipartBodyParts
     */
    public function setMaxMultipartBodyParts(bool|int $maxMultipartBodyParts): static
    {
        $this->maxMultipartBodyParts = $maxMultipartBodyParts;
        return $this;
    }

    /**
     * @return string
     */
    public function getMemoryLimit(): string
    {
        return $this->memoryLimit;
    }

    /**
     * @param string $memoryLimit
     */
    public function setMemoryLimit(string $memoryLimit): static
    {
        if (!validateDigits($memoryLimit)) {
            Logger::warning('Memory limit must be a positive value in bytes, ' .
                "or with standard PHP data size suffixes (K, M or G) e.g. '256M'");
            return $this;
        }
        $this->memoryLimit = $memoryLimit;
        return $this;
    }

    public function getPatterns(): array
    {
        $patterns = [];
        $reflectionOptions = new \ReflectionClass($this);
        foreach ($reflectionOptions->getProperties() as $property) {
            $value = $property->getValue($this);
            $patterns[toSnakeCase($property->getName())] = $value;
        }
        return $patterns;
    }


}