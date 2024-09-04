<?php declare(strict_types=1);

namespace EasyIni\Processors;

use EasyIni\Lang;
use EasyIni\Logger;
use EasyIni\PatternPairs;
use EasyIni\Ini\BooleanFormat;
use EasyIni\Options\ErrorHandlingOptions;

final class ErrorHandlingProcessor
{
    public static function process(
        string $ini,
        PatternPairs $patterns,
        ?ErrorHandlingOptions $options,
    ): void {
        if ($options === null) {
            Logger::debug(Lang::get('no_option', 'error handling'));
            return;
        }

        $i = 0;
        foreach ($options->iterEntries() as $name => $value) {
            if ($value->untouched())
                continue;

            $patterns->entry($name, $value, boolFormat: BooleanFormat::SWITCH);
            ++$i;
        }
        Logger::info(Lang::get('option_count', (string)$i, 'error handling'));
    }
}
