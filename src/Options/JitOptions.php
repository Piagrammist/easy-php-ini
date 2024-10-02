<?php declare(strict_types=1);

namespace EasyIni\Options;

use EasyIni\Lang;
use EasyIni\Logger;
use EasyIni\ErrorCounter;

use EasyIni\Ini\Entry;
use EasyIni\Ini\EntryState;
use EasyIni\Ini\EntryManager;
use EasyIni\Ini\ValueFormat;

use function EasyIni\digitCount;
use function EasyIni\validateBytes;

final class JitOptions extends EntryManager
{
    #[Entry]
    protected Entry $enable;

    #[Entry]
    protected Entry $enableCli;

    #[Entry('jit')]
    protected Entry $flags;

    #[Entry('jit_buffer_size')]
    protected Entry $bufferSize;

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

        $this->enable->setPrevValue('\d');
        $this->enableCli->setPrevValue('\d');
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
            // TODO: move this to the processing side
            $this->setDefaults();
        }
        return $this->setEntry($this->enable, $value, $state, ValueFormat::BOOL_BINARY);
    }

    public function setEnabledCli(
        ?bool $value = true,
        EntryState $state = EntryState::UNCOMMENT,
    ): self {
        if ($value) {
            // TODO: move this to the processing side
            $this->setDefaults();
        }
        return $this->setEntry($this->enableCli, $value, $state, ValueFormat::BOOL_BINARY);
    }

    public function setFlags(
        string|int|null $value = null,
        EntryState $state = EntryState::UNCOMMENT,
    ): self {
        if (
            $value !== null &&
            !(
                \is_int($value) && digitCount($value) === 4 ||
                \is_string($value) &&
                \in_array($value = \strtolower($value), self::$allowedStringFlags, true)
            )
        ) {
            Logger::error(Lang::get(
                'err_jit_flags',
                \implode(', ', self::$allowedStringFlags)
            ));
            ErrorCounter::increment();
        }

        return $this->setEntry($this->flags, $value, $state);
    }

    public function setBufferSize(
        string|int|null $value = null,
        EntryState $state = EntryState::UNCOMMENT,
    ): self {
        if ($value !== null && !validateBytes($value)) {
            Logger::error(Lang::get('err_bytes', 'JIT buffer-size'));
            ErrorCounter::increment();
        }

        return $this->setEntry($this->bufferSize, $value, $state);
    }
}
