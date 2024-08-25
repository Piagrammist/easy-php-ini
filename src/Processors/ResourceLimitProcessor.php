<?php declare(strict_types=1);

namespace EasyIni\Processors;

use EasyIni\Logger;
use EasyIni\PatternPairs;
use EasyIni\Options\ResourceLimitOptions;
use function EasyIni\pluralSuffix;

final class ResourceLimitProcessor
{
    public static function process(
        string $ini,
        PatternPairs $patterns,
        ?ResourceLimitOptions $options,
    ): void {
        if ($options === null) {
            Logger::debug('No resource limiting option provided.');
            return;
        }

        $i = 0;
        foreach ($options->iterEntries() as $key => $value) {
            $patterns->entry(
                $key,
                is_bool($value) ? '\2' : $value,
                comment: is_bool($value) && !$value
            );
            ++$i;
        }
        $s = pluralSuffix($i);
        Logger::info("Got $i resource limiting option$s.");
    }
}
