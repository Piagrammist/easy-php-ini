<?php declare(strict_types=1);

namespace EasyIni\Options;

use EasyIni\Lang;
use EasyIni\Logger;
use EasyIni\ErrorCounter;

use EasyIni\Ini\Entry;
use EasyIni\Ini\EntryState;
use EasyIni\Ini\EntryValue;
use EasyIni\Ini\EntryManager;

use function EasyIni\digitCount;
use function EasyIni\validateBytes;

final class JitOptions extends EntryManager
{
    #[Entry]
    protected EntryValue $enable;

    #[Entry]
    protected EntryValue $enableCli;

    #[Entry('jit')]
    protected EntryValue $flags;

    #[Entry('jit_buffer_size')]
    protected EntryValue $bufferSize;

    private static array $allowedStringFlags = [
        'disable',
        'on',
        'off',
        'tracing',
        'function',
    ];

    public function __construct()
    {
        parent::__construct();

        $this->flags->setValue('tracing');
        $this->bufferSize->setValue('64M');
    }

    private function setDefaults(): void
    {
        $this->flags->setStateIfUntouched(EntryState::UNCOMMENT);
        $this->bufferSize->setStateIfUntouched(EntryState::UNCOMMENT);
    }

    public function setEnabled(
        ?bool $value = true,
        EntryState $state = EntryState::UNCOMMENT,
    ): self {
        if ($value) {
            // TODO move this to the processing side
            $this->setDefaults();
        }
        return $this->setEntry($this->enable, $value, $state);
    }

    public function setEnabledCli(
        ?bool $value = true,
        EntryState $state = EntryState::UNCOMMENT,
    ): self {
        if ($value) {
            // TODO move this to the processing side
            $this->setDefaults();
        }
        return $this->setEntry($this->enableCli, $value, $state);
    }

    public function setFlags(
        string|int|null $value = null,
        EntryState $state = EntryState::UNCOMMENT,
    ): self {
        if (is_string($value)) {
            $value = strtolower($value);
        }
        return $this->setEntry($this->flags, $value, $state, static function ($value) {
            if (
                is_int($value) && digitCount($value) === 4 ||
                is_string($value) &&
                in_array($value, self::$allowedStringFlags, true)
            ) {
                return;
            }
            Logger::error(Lang::get(
                'err_jit_flags',
                implode(', ', self::$allowedStringFlags)
            ));
            ErrorCounter::increment();
        });
    }

    public function setBufferSize(
        string|int|null $value = null,
        EntryState $state = EntryState::UNCOMMENT,
    ): self {
        return $this->setEntry($this->bufferSize, $value, $state, static function ($value) {
            if (validateBytes($value))
                return;

            Logger::error(Lang::get('err_bytes', 'JIT buffer-size'));
            ErrorCounter::increment();
        });
    }
}
