<?php declare(strict_types=1);

namespace EasyIni\Processors;

use EasyIni\Logger;
use EasyIni\PatternPairs;
use function EasyIni\pluralSuffix;

final class DisablingProcessor
{
    public static function process(string $ini, PatternPairs $patterns, array $toDisable): void
    {
        $fnCount = count($toDisable['functions']);
        $classCount = count($toDisable['classes']);
        if ($fnCount !== 0) {
            $s = pluralSuffix($fnCount);
            Logger::info("Found $fnCount function$s to disable.");
            $patterns->basicEntry('disable_functions', implode(',', $toDisable['functions']));
        }
        if ($classCount !== 0) {
            $s = pluralSuffix($classCount, 'es');
            Logger::info("Found $classCount class$s to disable.");
            $patterns->basicEntry('disable_classes', implode(',', $toDisable['classes']));
        }
    }
}
