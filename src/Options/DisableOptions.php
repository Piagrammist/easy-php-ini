<?php declare(strict_types=1);

namespace EasyIni\Options;

use EasyIni\Lang;
use EasyIni\Logger;

use EasyIni\Ini\Entry;
use EasyIni\Ini\EntryState;
use EasyIni\Ini\EntryManager;
use EasyIni\Ini\ValueFormat;

final class DisableOptions extends EntryManager
{
    private bool $strict = true;

    #[Entry('disable_functions')]
    protected Entry $functions;

    #[Entry('disable_classes')]
    protected Entry $classes;

    public function setStrict(bool $strict = true): self
    {
        $this->strict = $strict;
        return $this;
    }

    public function setFunctions(
        ?array $value = null,
        EntryState $state = EntryState::UNCOMMENT,
    ): self {
        $value = array_unique(array_filter($value));
        if ($this->strict) {
            foreach ($value as $i => $fn) {
                if (!function_exists($fn)) {
                    Logger::warning(Lang::get('err_id_resolve', 'Function', $fn));
                    unset($value[$i]);
                }
            }
        }

        return $this->setEntry($this->functions, $value, $state, ValueFormat::ARR_CSV);
    }

    public function setClasses(
        ?array $value = null,
        EntryState $state = EntryState::UNCOMMENT,
    ): self {
        $value = array_unique(array_filter($value));
        if ($this->strict) {
            foreach ($value as $i => $class) {
                if (!class_exists($class)) {
                    Logger::warning(Lang::get('err_id_resolve', 'Class', $class));
                    unset($value[$i]);
                }
            }
        }

        return $this->setEntry($this->classes, $value, $state, ValueFormat::ARR_CSV);
    }
}
