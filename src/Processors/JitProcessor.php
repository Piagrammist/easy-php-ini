<?php declare(strict_types=1);

namespace EasyIni\Processors;

use EasyIni\Logger;
use EasyIni\PatternPairs;
use EasyIni\Ini\EntryState;
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
            in_array($options['enable'], [true, EntryState::UNCOMMENT], true) ||
            in_array($options['enable_cli'], [true, EntryState::UNCOMMENT], true)
        ) {
            $patterns->entry('zend_extension', prevValue: 'opcache');
        } elseif (
            in_array($options['enable'], [false, EntryState::COMMENT], true) ||
            in_array($options['enable_cli'], [false, EntryState::COMMENT], true)
        ) {
            $patterns->entry('zend_extension', prevValue: 'opcache', comment: true);
        }

        foreach (['enable', 'enable_cli'] as $entry) {
            $val = $options[$entry];
            if ($val === EntryState::UNTOUCHED)
                continue;

            $patterns->entry(
                "opcache\\.$entry",
                PatternPairs::entryValue($val, $val ? '1' : '0'),
                '\d',
                $val === EntryState::COMMENT,
            );
        }

        $toAdd = [];
        foreach (['jit', 'jit_buffer_size'] as $entry) {
            $val = $options[$entry];
            if ($val === EntryState::UNTOUCHED)
                continue;

            // See if flags/buffer-size entries already exist
            if (str_contains($ini, "opcache.$entry")) {
                $patterns->entry(
                    "opcache\\.$entry",
                    PatternPairs::entryValue($val),
                    '.*',
                    $val === EntryState::COMMENT,
                );
            } else {
                $toAdd[] = comment($val === EntryState::COMMENT) .
                    "opcache.$entry = $val";
                Logger::notice("No `opcache.$entry` entry found, proceeding to add.");
            }
        }
        if (count($toAdd) !== 0) {
            $toAdd = implode('', array_prefix(PHP_EOL . PHP_EOL, $toAdd));
            $patterns->entry('opcache\.enable_cli', "\\2$toAdd", '\d');
        }
        Logger::info('JIT processed.');
    }
}
