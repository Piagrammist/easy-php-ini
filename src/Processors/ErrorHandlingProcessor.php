<?php declare(strict_types=1);

namespace EasyIni\Processors;

use EasyIni\Logger;
use EasyIni\PatternPairs;
use EasyIni\Ini\BooleanFormat;
use EasyIni\Options\ErrorHandlingOptions;

use function EasyIni\pluralSuffix;

final class ErrorHandlingProcessor
{
    public static function process(
        string $ini,
        PatternPairs $patterns,
        ?ErrorHandlingOptions $options,
    ): void {
        if ($options === null) {
            Logger::debug('No error handling option provided.');
            return;
        }

        $i = 0;
        foreach ($options->iterEntries() as $name => $value) {
            if ($value->untouched())
                continue;

            $patterns->entry($name, $value, boolFormat: BooleanFormat::SWITCH);
            ++$i;
        }
        $s = pluralSuffix($i);
        Logger::info("Got $i error handling option$s.");
    }
}
