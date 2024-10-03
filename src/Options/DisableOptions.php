<?php declare(strict_types=1);

namespace EasyIni\Options;

use EasyIni\Lang;
use EasyIni\Logger;
use EasyIni\Strict;

use EasyIni\Ini\Entry;
use EasyIni\Ini\EntryState;
use EasyIni\Ini\EntryManager;
use EasyIni\Ini\ValueFormat;

use function EasyIni\filterArray;

final class DisableOptions extends EntryManager
{
    use Strict;

    #[Entry('disable_functions')]
    protected Entry $functions;

    #[Entry('disable_classes')]
    protected Entry $classes;

    public function setFunctions(
        ?array $value = null,
        EntryState $state = EntryState::UNCOMMENT,
    ): self {
        return $this->setAbstract(true, $value, $state);
    }

    public function setClasses(
        ?array $value = null,
        EntryState $state = EntryState::UNCOMMENT,
    ): self {
        return $this->setAbstract(false, $value, $state);
    }

    private function setAbstract(bool $isFn, ?array $value, EntryState $state): self
    {
        $prop = $isFn ? $this->functions : $this->classes;
        if (empty($value)) {
            $prop->setState($state);
            return $this;
        }

        $value = filterArray($value);
        if ($this->isStrict()) {
            $mode = $isFn ? 'Function' : 'Class';
            $validator = $isFn ? \function_exists(...) : \class_exists(...);
            foreach ($value as $i => $name) {
                if (!$validator($name)) {
                    Logger::warning(Lang::get('err_id_resolve', $mode, $name));
                    unset($value[$i]);
                }
            }
        }
        return $this->setEntry($prop, $value, $state, ValueFormat::ARR_CSV);
    }
}
