<?php declare(strict_types=1);

namespace EasyIni\Processors;

use EasyIni\Logger;
use EasyIni\PatternPairs;
use EasyIni\Options\JitOptions;

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

        $fullyDisable = !($options->getEnabled() || $options->getEnabledCli());
        $patterns->entry('zend_extension', '\2', 'opcache', $fullyDisable);
        $patterns->entry('opcache\.enable', '1', '\d', !$options->getEnabled());
        $patterns->entry('opcache\.enable_cli', '1', '\d', !$options->getEnabledCli());
        if ($fullyDisable) {
            Logger::notice('JIT will be fully disabled!');
            return;
        }

        // See if flags/buffer-size entries already exist
        $toAdd = [];
        if (str_contains($ini, 'opcache.jit')) {
            $patterns->entry('opcache\.jit', $options->getFlags());
        } else {
            $toAdd[] = "opcache.jit = {$options->getFlags()}";
            Logger::notice('No `opcache.jit` entry found, proceeding to add.');
        }
        if (str_contains($ini, 'opcache.jit_buffer_size')) {
            $patterns->entry('opcache\.jit_buffer_size', $options->getBufferSize());
        } else {
            $toAdd[] = "opcache.jit_buffer_size = {$options->getBufferSize()}";
            Logger::notice('No `opcache.jit_buffer_size` entry found, proceeding to add.');
        }
        if (count($toAdd) !== 0) {
            $toAdd = implode('', array_prefix("\n\n", $toAdd));
            $patterns->entry('opcache\.enable_cli', "\\2$toAdd", '\d');
        }
        Logger::info('JIT processed.');
    }
}
