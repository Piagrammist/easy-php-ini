<?php declare(strict_types=1);

namespace EasyIni\Processors;

use EasyIni\Lang;
use EasyIni\Logger;
use EasyIni\PatternPairs;
use EasyIni\Options\JitOptions;

use function EasyIni\comment;
use function EasyIni\array_prefix;

final class JitProcessor
{
    public static function process(
        string $ini,
        PatternPairs $patterns,
        ?JitOptions $options,
    ): void {
        if ($options === null) {
            Logger::debug(Lang::get('no_option', 'JIT'));
            return;
        }

        $options = $options->getEntries();
        $enable = $options['enable'];
        $enableCli = $options['enable_cli'];
        if (
            $enable->getRawValue() === true || $enable->toUncomment() ||
            $enableCli->getRawValue() === true || $enableCli->toUncomment()
        ) {
            $patterns->basicEntry('zend_extension', prevValue: 'opcache');
        } elseif (
            $enable->getRawValue() === false || $enable->toComment() ||
            $enableCli->getRawValue() === false || $enableCli->toComment()
        ) {
            $patterns->basicEntry('zend_extension', prevValue: 'opcache', comment: true);
        }

        foreach (['enable', 'enable_cli'] as $entry) {
            $val = $options[$entry];
            if ($val->untouched())
                continue;

            $patterns->entry("opcache\\.$entry", $val, '\d');
        }

        $toAdd = [];
        foreach (['jit', 'jit_buffer_size'] as $entry) {
            $val = $options[$entry];
            if ($val->untouched())
                continue;

            // See if flags/buffer-size entries already exist
            if (str_contains($ini, "opcache.$entry")) {
                $patterns->entry("opcache\\.$entry", $val);
            } else {
                $toAdd[] = comment($val->toComment()) .
                    "opcache.$entry = {$val->getValue()}";
                Logger::notice(Lang::get('entry_add', "opcache.$entry"));
            }
        }
        if (count($toAdd) !== 0) {
            $toAdd = implode('', array_prefix(PHP_EOL . PHP_EOL, $toAdd));
            $patterns->basicEntry(
                'opcache\.enable_cli',
                "\\2$toAdd",
                '\d',
                $enableCli->toComment()
            );
        }
        Logger::info(Lang::get('jit_processed'));
    }
}
