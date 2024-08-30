<?php declare(strict_types=1);

namespace EasyIni\Options;

use EasyIni\Lang;
use EasyIni\Logger;
use EasyIni\ErrorCounter;

use EasyIni\Ini\Entry;
use EasyIni\Ini\EntryState;
use EasyIni\Ini\EntryValue;
use EasyIni\Ini\EntryManager;

use function EasyIni\validateBytes;

final class ResourceLimitOptions extends EntryManager
{
    #[Entry]
    protected EntryValue $maxInputTime;

    #[Entry]
    protected EntryValue $maxInputVars;

    #[Entry]
    protected EntryValue $maxExecutionTime;

    #[Entry]
    protected EntryValue $maxInputNestingLevel;

    #[Entry]
    protected EntryValue $maxMultipartBodyParts;

    #[Entry]
    protected EntryValue $memoryLimit;

    public function setMaxInputTime(
        ?int $value = null,
        EntryState $state = EntryState::UNCOMMENT,
    ): self {
        return $this->setEntry($this->maxInputTime, $value, $state);
    }

    public function setMaxInputVars(
        ?int $value = null,
        EntryState $state = EntryState::UNCOMMENT,
    ): self {
        return $this->setEntry($this->maxInputVars, $value, $state);
    }

    public function setMaxExecutionTime(
        ?int $value = null,
        EntryState $state = EntryState::UNCOMMENT,
    ): self {
        return $this->setEntry($this->maxExecutionTime, $value, $state);
    }

    public function setMaxInputNestingLevel(
        ?int $value = null,
        EntryState $state = EntryState::UNCOMMENT,
    ): self {
        return $this->setEntry($this->maxInputNestingLevel, $value, $state);
    }

    public function setMaxMultipartBodyParts(
        ?int $value = null,
        EntryState $state = EntryState::UNCOMMENT,
    ): self {
        return $this->setEntry($this->maxMultipartBodyParts, $value, $state);
    }

    public function setMemoryLimit(
        string|int|null $value = null,
        EntryState $state = EntryState::UNCOMMENT,
    ): self {
        return $this->setEntry($this->memoryLimit, $value, $state, static function ($value) {
            if (validateBytes($value))
                return;

            Logger::error(Lang::get('error_bytes', 'Memory limit'));
            ErrorCounter::increment();
        });
    }
}
