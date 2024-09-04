<?php declare(strict_types=1);

namespace EasyIni\Processors;

use EasyIni\Lang;
use EasyIni\Logger;
use EasyIni\PatternPairs;

final class DisablingProcessor
{
    public static function process(string $ini, PatternPairs $patterns, array $toDisable): void
    {
        $fnCount = count($toDisable['functions']);
        $classCount = count($toDisable['classes']);
        if ($fnCount !== 0) {
            Logger::info(Lang::get('disable_fn', (string)$fnCount));
            $patterns->basicEntry('disable_functions', implode(',', $toDisable['functions']));
        }
        if ($classCount !== 0) {
            Logger::info(Lang::get('disable_cls', (string)$classCount));
            $patterns->basicEntry('disable_classes', implode(',', $toDisable['classes']));
        }
    }
}
