<?php declare(strict_types=1);

namespace EasyIni\Options;

use EasyIni\Ini\Entry;
use EasyIni\Ini\EntryState;
use EasyIni\Ini\EntryManager;
use EasyIni\Ini\ValueFormat;

final class ErrorHandlingOptions extends EntryManager
{
    #[Entry]
    protected Entry $displayErrors;

    #[Entry]
    protected Entry $displayStartupErrors;

    #[Entry]
    protected Entry $logErrors;

    #[Entry]
    protected Entry $htmlErrors;

    #[Entry]
    protected Entry $errorLog;

    public function setDisplayErrors(
        ?bool $value = true,
        EntryState $state = EntryState::UNCOMMENT,
    ): self {
        return $this->setEntry($this->displayErrors, $value, $state, ValueFormat::BOOL_SWITCH);
    }

    public function setDisplayStartupErrors(
        ?bool $value = true,
        EntryState $state = EntryState::UNCOMMENT,
    ): self {
        return $this->setEntry($this->displayStartupErrors, $value, $state, ValueFormat::BOOL_SWITCH);
    }

    public function setLogErrors(
        ?bool $value = true,
        EntryState $state = EntryState::UNCOMMENT,
    ): self {
        return $this->setEntry($this->logErrors, $value, $state, ValueFormat::BOOL_SWITCH);
    }

    public function setHtmlErrors(
        ?bool $value = true,
        EntryState $state = EntryState::UNCOMMENT,
    ): self {
        return $this->setEntry($this->htmlErrors, $value, $state, ValueFormat::BOOL_SWITCH);
    }

    public function setLogFile(
        ?string $value = null,
        EntryState $state = EntryState::UNCOMMENT,
    ): self {
        return $this->setEntry($this->errorLog, $value, $state);
    }
}
