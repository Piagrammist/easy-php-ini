<?php declare(strict_types=1);

namespace EasyIni;

class Processor extends Ini
{
    private bool $__setup = false;
    protected array $extensions = [];
    protected ?JITOptions $jit = null;
    protected ?CommonOptions $common = null;
    protected PatternPairs $patterns;

    public function __construct()
    {
        $this->patterns = new PatternPairs;
        if (static::IS_WIN) {
            $this->patterns->set('ext_dir', '~;(extension_dir) *= *"(ext)"~', '\1 = "\2"');
        }
        // $this->detectFPM();
    }
    private function detectFPM()
    {
        $is_fpm_running = false;
        $output = [];

        if (static::IS_WIN) {
            // Check for php-fpm on Windows
            exec('tasklist /FI "IMAGENAME eq php-fpm.exe"', $output);
            exec('tasklist /FI "IMAGENAME eq php-cgi.exe"', $output);
        } else {
            // Check for php-fpm on Unix/Linux for multiple versions
            exec('ps aux | grep -E "php[0-9.]*-fpm|php-fpm" | grep -v grep', $output);
        }

        if (!empty($output)) {
            $is_fpm_running = true;
        }
        if ($is_fpm_running) {
            Logger::warning('`php-fpm` detected! if you want to change these settings in fpm/php.ini you must run this script in web!');
        }
    }

    public function setup(): bool
    {
        if ($this->__setup) {
            throw new \BadMethodCallException('Cannot setup more than once');
        }
        Logger::info('Env mode: ' . ($this->dev ? 'development' : 'production'));
        $this->parse();
        $this->__setup = true;
        Logger::info("Using '{$this->getIniPath(template: true)}' as template.");
        $res = $this->writeIni(
            preg_replace(
                $this->patterns->get('lookups'),
                $this->patterns->get('replacements'),
                $this->readIni()
            )
        );
        Logger::info('Done!');
        return $res;
    }

    protected function parse(): void
    {
        $this->processExtensions();
        $this->processDevOptions();
        $this->processCommonOptions();
        $this->processJIT();
    }

    protected function processExtensions(): void
    {
        if (count($this->extensions) === 0) {
            Logger::info('No extension found!');
            return;
        }
        if (!static::IS_WIN) {
            Logger::notice('Extension handling is only supported on Windows. Skipping...');
            return;
        }
        $this->patterns->set(
            'extensions',
            '~;(extension) *= *(' . implode('|', $this->extensions) . ')~',
            '\1=\2'
        );
        Logger::info('Found ' . count($this->extensions) . ' extensions.');
    }

    protected function processDevOptions(): void
    {
        if (!$this->dev) {
            return;
        }
        // Register `$argv`
        $this->patterns->set('argv', '~;?(register_argc_argv) *= *Off~', '\1 = On');
        // Unlock PHAR editing
        $this->patterns->set('phar_readonly', '~;?(phar\.readonly) *= *On~', '\1 = Off');
    }

    protected function processCommonOptions(): void
    {
        $common = $this->common;
        if ($common === null) {
            Logger::info('Common options will not be processed.');
            return;
        }

        Logger::info('Processing common options.');
        foreach ($common->getProps() as $key => $value) {
            $this->patterns->set(
                $key,
                "~;?$key *= *(.+)~",
                is_bool($value) ? self::comment(!$value) . "$key = \\1" : "$key = $value"
            );
        }
    }

    protected function processJIT(): void
    {
        $jit = $this->jit;
        if ($jit === null) {
            Logger::info('JIT will not be processed.');
            return;
        }

        Logger::info('Processing JIT.');
        $fullyDisable = !($jit->getEnabled() || $jit->getEnabledCLI());
        $this->patterns->set(
            'opcache',
            '~;?(zend_extension) *= *(opcache)~',
            self::comment($fullyDisable) . '\1=\2'
        );
        $this->patterns->set(
            'opcache_enable',
            '~;?(opcache\.enable) *= *\d~',
            self::comment(!$jit->getEnabled()) . '\1=1'
        );
        $this->patterns->set(
            'opcache_enable_cli',
            '~;?(opcache\.enable_cli) *= *\d~',
            self::comment(!$jit->getEnabledCLI()) . '\1=1'
        );
        if ($fullyDisable) {
            Logger::info('JIT will be fully disabled!');
            return;
        }

        // See if flags/buffer-size entries already exist
        $toAdd = [];
        $ini = $this->readIni();
        if (str_contains($ini, 'opcache.jit')) {
            $this->patterns->set('jit', '~;?(opcache\.jit) *= *.+~', "\\1={$jit->getFlags()}");
            Logger::debug('Found already existing `opcache.jit`.');
        } else {
            $toAdd[] = "opcache.jit={$jit->getFlags()}";
            Logger::debug('No `opcache.jit` found, proceeding to add.');
        }
        if (str_contains($ini, 'opcache.jit_buffer_size')) {
            $this->patterns->set(
                'jit_bugger_size',
                '~;?(opcache\.jit_buffer_size) *= *.+~',
                "\\1={$jit->getBufferSize()}"
            );
            Logger::debug('Found already existing `opcache.jit_buffer_size`.');
        } else {
            $toAdd[] = "opcache.jit_buffer_size={$jit->getBufferSize()}";
            Logger::debug('No `opcache.jit_buffer_size` found, proceeding to add.');
        }
        if (count($toAdd) !== 0) {
            $toAdd = implode('', array_prefix("\n\n", $toAdd));
            $this->patterns->set('jit_entries', '~(;?opcache\.enable_cli) *= *(\d)~', "\\1=\\2$toAdd");
        }
    }

    public function setExtensions(string ...$extensions): static
    {
        $this->extensions = array_unique(array_map('strtolower', array_filter($extensions)));
        return $this;
    }
    public function addExtension(string $ext): static
    {
        if ($ext !== '' && !in_array($ext = strtolower($ext), $this->extensions, true)) {
            $this->extensions[] = $ext;
        }
        return $this;
    }

    public function setCommon(CommonOptions $options): static
    {
        $this->common = $options;
        return $this;
    }

    public function setJIT(JITOptions|bool $jit = true): static
    {
        if (is_bool($jit)) {
            $tmp = $jit;
            $jit = new JITOptions;
            if ($tmp === true) {
                $jit->setEnabled()
                    ->setEnabledCLI();
            }
        }
        $this->jit = $jit;
        return $this;
    }
}
