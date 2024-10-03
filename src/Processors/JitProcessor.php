<?php declare(strict_types=1);

namespace EasyIni\Processors;

use EasyIni\Lang;
use EasyIni\Logger;
use EasyIni\PatternPairs;
use EasyIni\Options\JitOptions;

use function EasyIni\comment;
use function EasyIni\prefixArray;

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
        foreach ([$enable, $enableCli] as $entry) {
            if (!$entry->untouched()) {
                $this->patterns->entry($entry);
            }
        }

        $toAdd = [];
        foreach (['jit', 'jit_buffer_size'] as $name) {
            /** @var \EasyIni\Ini\Entry $entry */
            $entry = $options[$name];
            if ($entry->untouched())
                continue;

            // See if flags/buffer-size entries already exist
            if (\str_contains($this->ini, $entry->getFullName())) {
                $this->patterns->entry($entry);
            } else {
                $toAdd[] = comment($entry->toComment()) .
                    $entry->getFullName() . ' = ' . $entry->getValue();
                Logger::notice(Lang::get('entry_add', $entry->getFullName()));
            }
        }
        if (\count($toAdd)) {
            $toAdd = \implode('', prefixArray(\PHP_EOL . \PHP_EOL, $toAdd));
            $new = clone $enableCli;
            $new->setValue("\\2$toAdd");
            $this->patterns->entry($new);
        }
        Logger::info(Lang::get('jit_processed'));
    }
}
