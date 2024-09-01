<?php declare(strict_types=1);

namespace EasyIni\Processors;

use EasyIni\Logger;
use EasyIni\PatternPairs;

use const EasyIni\IS_WIN;

final class ExtensionProcessor
{
    public static function process(
        string $ini,
        PatternPairs $patterns,
        array $extensions,
    ): void {
        if (count($extensions) === 0) {
            Logger::debug('No extension provided!');
            return;
        }
        if (!IS_WIN) {
            Logger::notice('Extension handling is only supported on Windows. Skipping...');
            return;
        }
        $patterns->basicEntry('extension_dir', prevValue: '"ext"');
        $patterns->basicEntry('extension', prevValue: implode('|', $extensions));
        Logger::info('Got ' . count($extensions) . ' extensions.');
    }
}
