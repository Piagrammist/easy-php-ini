<?php declare(strict_types=1);

namespace EasyIni\Options;

use EasyIni\Lang;
use EasyIni\Logger;
use EasyIni\Strict;
use EasyIni\ErrorCounter;

use EasyIni\Ini\Entry;
use EasyIni\Ini\EntryState;
use EasyIni\Ini\EntryManager;
use EasyIni\Ini\ValueFormat;

use function EasyIni\filterArray;

final class PharOptions extends EntryManager
{
    use Strict;

    protected static ?string $namespace = 'phar';

    #[Entry]
    protected Entry $readonly;

    #[Entry]
    protected Entry $requireHash;

    #[Entry]
    protected Entry $cacheList;

    public function setReadonly(
        ?bool $value = null,
        EntryState $state = EntryState::UNCOMMENT,
    ): self {
        return $this->setEntry($this->readonly, $value, $state, ValueFormat::BOOL_SWITCH);
    }

    public function setRequireHash(
        ?bool $value = null,
        EntryState $state = EntryState::UNCOMMENT,
    ): self {
        return $this->setEntry($this->requireHash, $value, $state, ValueFormat::BOOL_SWITCH);
    }

    public function setCacheList(
        ?array $value = null,
        EntryState $state = EntryState::UNCOMMENT,
    ): self {
        if (!empty($value)) {
            $value = filterArray($value);
        }
        if ($this->isStrict()) {
            foreach ($value as $path) {
                if (!\realpath($path)) {
                    Logger::error(Lang::get('err_file_resolve', $path));
                    ErrorCounter::increment();
                }
            }
        }
        return $this->setEntry($this->cacheList, $value, $state, ValueFormat::ARR_PATH);
    }
}
