<?php declare(strict_types=1);

namespace EasyIni\Options;

use EasyIni\Ini\Entry;
use EasyIni\Ini\EntryState;
use EasyIni\Ini\EntryManager;
use EasyIni\Ini\ValueFormat;

use function EasyIni\filterArray;

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
        $value = filterArray($value);
        if ($value) {
            // ! have a look at this if you changed `setExtensionDir()`
            $this->setExtensionDir();
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
