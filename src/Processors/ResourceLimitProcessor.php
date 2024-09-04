<?php declare(strict_types=1);

namespace EasyIni\Processors;

use EasyIni\Lang;
use EasyIni\Logger;
use EasyIni\PatternPairs;
use EasyIni\Options\ResourceLimitOptions;

final class ResourceLimitProcessor
{
    public static function process(
        string $ini,
        PatternPairs $patterns,
        ?ResourceLimitOptions $options,
    ): void {
        if ($options === null) {
            Logger::debug(Lang::get('no_option', 'resource limiting'));
            return;
        }

        $i = 0;
        foreach ($options->iterEntries() as $name => $value) {
            if ($value->untouched())
                continue;

            $patterns->entry($name, $value);
            ++$i;
        }
        Logger::info(Lang::get('option_count', (string)$i, 'resource limiting'));
    }
}
