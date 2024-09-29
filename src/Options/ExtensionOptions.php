<?php declare(strict_types=1);

namespace EasyIni\Options;

use EasyIni\Ini\Entry;
use EasyIni\Ini\EntryState;
use EasyIni\Ini\EntryManager;
use EasyIni\Ini\ValueFormat;

final class ExtensionOptions extends EntryManager
{
    #[Entry]
    protected Entry $extension;

    #[Entry]
    protected Entry $extensionDir;

    public function setExtensions(
        ?array $value = null,
        EntryState $state = EntryState::UNCOMMENT,
    ): self {
        $value = array_unique(array_filter($value));
        if ($value) {
            $this->extensionDir->setPrevValue('"ext"');
            $this->extensionDir->setState(EntryState::UNCOMMENT);
        }
        $this->extension->setState($state);
        $this->extension->setPrevValue($value, ValueFormat::ARR_REGEX);
        return $this;
    }

    public function setExtensionDir(
        ?string $value = null,
        EntryState $state = EntryState::UNCOMMENT,
    ): self {
        $this->setEntry($this->extensionDir, $value, $state);
        // TODO: somehow stop depending on the `"ext"` value to find the entry
        // ! there are 2 `extension_dir` entries with different values.
        $this->extensionDir->setPrevValue('"ext"');
        return $this;
    }
}
