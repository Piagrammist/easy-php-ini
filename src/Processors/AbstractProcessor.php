<?php declare(strict_types=1);

namespace EasyIni\Processors;

use EasyIni\Lang;
use EasyIni\Logger;
use EasyIni\PatternPairs;
use EasyIni\Ini\EntryManager;

abstract class AbstractProcessor implements \Stringable
{
    protected static int $limit = 1;
    protected static string $name;
    protected static string $optionsClass;

    protected ?PatternPairs $patterns = null;

    public function __construct(protected string $ini)
    {
        if (!\class_exists(static::$optionsClass)) {
            throw new \Exception(Lang::get('err_class_resolve', static::$optionsClass));
        }
    }

    public function apply(?EntryManager $options): void
    {
        if ($options === null) {
            Logger::debug(Lang::get('no_option', \strtolower(static::$name)));
            return;
        }
        if ($options::class !== static::$optionsClass) {
            throw new \InvalidArgumentException(
                Lang::get('err_options_cls', $options::class, static::$optionsClass)
            );
        }

        $i = 0;
        $this->patterns = new PatternPairs;
        foreach ($options->iterEntries() as $entry) {
            if ($entry->untouched())
                continue;

            $this->patterns->entry($entry);
            ++$i;
        }
        Logger::debug(Lang::get('option_count', (string)$i, \strtolower(static::$name)));
    }

    public function replace(): string
    {
        if (!$this->patterns) {
            return $this->ini;
        }
        return \preg_replace(
            $this->patterns->getLookups(),
            $this->patterns->getReplacements(),
            $this->ini,
            static::$limit,
        );
    }

    public function __toString(): string
    {
        return static::$name;
    }
}
