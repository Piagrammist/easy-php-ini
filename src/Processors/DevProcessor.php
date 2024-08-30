<?php declare(strict_types=1);

namespace EasyIni\Processors;

use EasyIni\PatternPairs;

final class DevProcessor
{
    public static function process(string $ini, PatternPairs $patterns, bool $dev): void
    {
        if (!$dev) {
            return;
        }
        // Unlock PHAR editing
        $patterns->basicEntry('phar\.readonly', 'Off');
    }
}
