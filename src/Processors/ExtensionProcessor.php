<?php declare(strict_types=1);

namespace EasyIni\Processors;

use EasyIni\Lang;
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
            Logger::debug(Lang::get('no_x', 'extension'));
            return;
        }
        if (!IS_WIN) {
            Logger::notice(Lang::get('err_win_no_ext'));
            return;
        }
        $patterns->basicEntry('extension_dir', prevValue: '"ext"');
        $patterns->basicEntry('extension', prevValue: implode('|', $extensions));
        Logger::info(Lang::get('count', (string)count($extensions), 'extension(s)'));
    }
}
