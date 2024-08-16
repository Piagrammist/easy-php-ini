<?php declare(strict_types=1);

namespace EasyIni;

class JITOptions
{
    protected bool $enabled = false;
    protected bool $enabledCLI = false;
    protected string|int $flags = 'tracing';
    protected string|int $bufferSize = '64M';

    protected static array $allowedStringFlags = [
        'disable',
        'on',
        'off',
        'tracing',
        'function',
    ];

    public function setEnabled(bool $enable = true): static
    {
        $this->enabled = $enable;
        return $this;
    }
    public function getEnabled(): bool
    {
        return $this->enabled;
    }

    public function setEnabledCLI(bool $enable = true): static
    {
        $this->enabledCLI = $enable;
        return $this;
    }
    public function getEnabledCLI(): bool
    {
        return $this->enabledCLI;
    }

    public function setFlags(string|int $flags): static
    {
        if (!((is_int($flags) && digitCount($flags) === 4) ||
            (is_string($flags) && in_array($flags = strtolower($flags), static::$allowedStringFlags, true)))) {
            throw new \InvalidArgumentException('JIT flags must be a 4 digit number or ' .
                'one of "' . implode(', ', static::$allowedStringFlags) . '"');
        }
        $this->flags = $flags;
        return $this;
    }
    public function getFlags(): string|int
    {
        return $this->flags;
    }

    public function setBufferSize(string|int $size): static
    {
        if (!preg_match('~^\d+[KMG]?$~i', $size)) {
            throw new \InvalidArgumentException('JIT buffer size must be a positive value in bytes, ' .
                "or with standard PHP data size suffixes (K, M or G) e.g. '256M'");
        }
        $this->bufferSize = $size;
        return $this;
    }
    public function getBufferSize(): string|int
    {
        return $this->bufferSize;
    }
}
