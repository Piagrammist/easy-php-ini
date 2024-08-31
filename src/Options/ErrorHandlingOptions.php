<?php declare(strict_types=1);

namespace EasyIni\Options;

use EasyIni\Ini\Entry;
use EasyIni\Ini\EntryState;
use EasyIni\Ini\EntryValue;
use EasyIni\Ini\EntryManager;

final class ErrorHandlingOptions extends EntryManager
{
    #[Entry]
    protected EntryValue $displayErrors;

    #[Entry]
    protected EntryValue $displayStartupErrors;

    #[Entry]
    protected EntryValue $logErrors;

    #[Entry]
    protected EntryValue $htmlErrors;

    #[Entry]
    protected EntryValue $errorLog;

    public function setDisplayErrors(
        ?bool $value = true,
        EntryState $state = EntryState::UNCOMMENT,
    ): self {
        return $this->setEntry($this->displayErrors, $value, $state);
    }

    public function setDisplayStartupErrors(
        ?bool $value = true,
        EntryState $state = EntryState::UNCOMMENT,
    ): self {
        return $this->setEntry($this->displayStartupErrors, $value, $state);
    }

    public function setLogErrors(
        ?bool $value = true,
        EntryState $state = EntryState::UNCOMMENT,
    ): self {
        return $this->setEntry($this->logErrors, $value, $state);
    }

    public function setHtmlErrors(
        ?bool $value = true,
        EntryState $state = EntryState::UNCOMMENT,
    ): self {
        return $this->setEntry($this->htmlErrors, $value, $state);
    }

    public function setLogFile(
        ?string $value = null,
        EntryState $state = EntryState::UNCOMMENT,
    ): self {
        return $this->setEntry($this->errorLog, $value, $state);
    }
}
