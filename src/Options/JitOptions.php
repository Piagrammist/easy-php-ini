<?php declare(strict_types=1);

namespace EasyIni\Options;

use EasyIni\Logger;
use EasyIni\ErrorCounter;

use EasyIni\Ini\Entry;
use EasyIni\Ini\Entries;
use EasyIni\Ini\EntryState;

use function EasyIni\digitCount;
use function EasyIni\validateBytes;

final class JitOptions
{
    use Entries;

    #[Entry]
    private $enable = EntryState::UNTOUCHED;

    #[Entry]
    private $enableCli = EntryState::UNTOUCHED;

    #[Entry('jit')]
    private $flags = EntryState::UNTOUCHED;

    #[Entry('jit_buffer_size')]
    private $bufferSize = EntryState::UNTOUCHED;

    private static array $allowedStringFlags = [
        'disable',
        'on',
        'off',
        'tracing',
        'function',
    ];

    private function setDefaults(): void
    {
        if ($this->flags === EntryState::UNTOUCHED)
            $this->flags = 'tracing';

        if ($this->bufferSize === EntryState::UNTOUCHED)
            $this->bufferSize = '64M';
    }

    public function setEnabled(EntryState|bool $enable = true): self
    {
        $this->enable = $enable;
        $this->setDefaults();
        return $this;
    }

    public function setEnabledCli(EntryState|bool $enable = true): self
    {
        $this->enableCli = $enable;
        $this->setDefaults();
        return $this;
    }

    public function setFlags(EntryState|string|int $flags): self
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

    public function setBufferSize(EntryState|string|int $size): self
    {
        if (!validateBytes($size)) {
            Logger::error('JIT buffer size must be a positive value in bytes, ' .
                "or with standard PHP data size suffixes (K, M or G) e.g. '256M'");
            ErrorCounter::increment();
        }
        $this->bufferSize = $size;
        return $this;
    }
}
