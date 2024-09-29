<?php declare(strict_types=1);

namespace EasyIni\Processors;

use EasyIni\Lang;
use EasyIni\Logger;
use EasyIni\PatternPairs;
use EasyIni\Options\JitOptions;

use function EasyIni\comment;
use function EasyIni\array_prefix;

final class JitProcessor extends AbstractProcessor
{
    protected static string $name = 'JIT';
    protected static string $optionsClass = JitOptions::class;

    #[\Override]
    public function apply($options): void
    {
        if ($options === null) {
            Logger::debug(Lang::get('no_option', 'JIT'));
            return;
        }
        if ($options::class !== static::$optionsClass) {
            throw new \InvalidArgumentException(
                Lang::get('err_options_cls', $options::class, static::$optionsClass)
            );
        }

        $this->patterns = new PatternPairs;

        $options = $options->getEntries();
        $enable = $options['enable'];
        $enableCli = $options['enable_cli'];
        if (
            $enable->getRawValue() || $enable->toUncomment() ||
            $enableCli->getRawValue() || $enableCli->toUncomment()
        ) {
            $this->patterns->basicEntry('zend_extension', prevValue: 'opcache');
        } elseif (
            (!$enable->getRawValue() || $enable->toComment()) &&
            (!$enableCli->getRawValue() || $enableCli->toComment())
        ) {
            $this->patterns->basicEntry('zend_extension', prevValue: 'opcache', comment: true);
        }

        foreach (['enable', 'enable_cli'] as $name) {
            $entry = $options[$name];
            if ($entry->untouched())
                continue;

            $this->patterns->entry("opcache\\.$name", $entry);
        }

        $toAdd = [];
        foreach (['jit', 'jit_buffer_size'] as $name) {
            $entry = $options[$name];
            if ($entry->untouched())
                continue;

            // See if flags/buffer-size entries already exist
            if (\str_contains($this->ini, "opcache.$name")) {
                $this->patterns->entry("opcache\\.$name", $entry);
            } else {
                $toAdd[] = comment($entry->toComment()) .
                    "opcache.$name = {$entry->getValue()}";
                Logger::notice(Lang::get('entry_add', "opcache.$name"));
            }
        }
        if (\count($toAdd)) {
            $toAdd = \implode('', array_prefix(\PHP_EOL . \PHP_EOL, $toAdd));
            $this->patterns->basicEntry(
                'opcache\.enable_cli',
                "\\2$toAdd",
                '\d',
                $enableCli->toComment()
            );
        }
        Logger::info(Lang::get('jit_processed'));
    }
}
