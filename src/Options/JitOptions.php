<?php declare(strict_types=1);

namespace EasyIni\Options;

use EasyIni\Logger;
use EasyIni\ErrorCounter;
use function EasyIni\digitCount;
use function EasyIni\validateBytes;

final class JitOptions
{
    private bool $enabled = false;
    private bool $enabledCli = false;
    private string|int $flags = 'tracing';
    private string|int $bufferSize = '64M';

    private static array $allowedStringFlags = [
        'disable',
        'on',
        'off',
        'tracing',
        'function',
    ];

    public function setEnabled(bool $enable = true): self
    {
        $this->enabled = $enable;
        return $this;
    }
    public function getEnabled(): bool
    {
        return $this->enabled;
    }

    public function setEnabledCli(bool $enable = true): self
    {
        $this->enabledCli = $enable;
        return $this;
    }
    public function getEnabledCli(): bool
    {
        return $this->enabledCli;
    }

    public function setFlags(string|int $flags): self
    {
        if (
            !((is_int($flags) && digitCount($flags) === 4) ||
                (is_string($flags) &&
                    in_array($flags = strtolower($flags), self::$allowedStringFlags, true)))
        ) {
            Logger::error('JIT flags must be a 4 digit number or ' .
                'one of "' . implode(', ', self::$allowedStringFlags) . '"');
            ErrorCounter::increment();
        }
        $this->flags = $flags;
        return $this;
    }
    public function getFlags(): string|int
    {
        return $this->flags;
    }

    public function setBufferSize(string|int $size): self
    {
        if (!validateBytes($size)) {
            Logger::error('JIT buffer size must be a positive value in bytes, ' .
                "or with standard PHP data size suffixes (K, M or G) e.g. '256M'");
            ErrorCounter::increment();
        }
        $this->bufferSize = $size;
        return $this;
    }
    public function getBufferSize(): string|int
    {
        return $this->bufferSize;
    }
}
