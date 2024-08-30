<?php declare(strict_types=1);

namespace EasyIni\Processors;

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
            Logger::debug('No JIT option provided.');
            return;
        }
        $options = $options->getEntries();

        if (
            $options['enable']->getRawValue() === true ||
            $options['enable']->toUncomment() ||
            $options['enable_cli']->getRawValue() === true ||
            $options['enable_cli']->toUncomment()
        ) {
            $patterns->basicEntry('zend_extension', prevValue: 'opcache');
        } elseif (
            $options['enable']->getRawValue() === false ||
            $options['enable']->toComment() ||
            $options['enable_cli']->getRawValue() === false ||
            $options['enable_cli']->toComment()
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
                Logger::notice("No `opcache.$entry` entry found, proceeding to add.");
            }
        }
        if (count($toAdd) !== 0) {
            $toAdd = implode('', array_prefix(PHP_EOL . PHP_EOL, $toAdd));
            $patterns->basicEntry(
                'opcache\.enable_cli',
                "\\2$toAdd",
                '\d',
                $options['enable_cli']->toComment()
            );
        }
        Logger::info('JIT processed.');
    }
}
