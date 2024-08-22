<?php declare(strict_types=1);

namespace EasyIni;

use EasyIni\Options\JITOptions;
use EasyIni\Options\CommonOptions;

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
            $this->patterns->entry('extension_dir', prevValue: '"ext"');
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
        $this->process();
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

    protected function process(): void
    {
        $this->processExtensions();
        $this->processDev();
        $this->processCommon();
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
        $this->patterns->entry('extension', prevValue: implode('|', $this->extensions));
        Logger::info('Found ' . count($this->extensions) . ' extensions.');
    }

    protected function processDev(): void
    {
        if (!$this->dev) {
            return;
        }
        // Register `$argv`
        $this->patterns->entry('register_argc_argv', 'On');
        // Unlock PHAR editing
        $this->patterns->entry('phar\.readonly', 'Off');
    }

    protected function processCommon(): void
    {
        $options = $this->common;
        if ($options === null) {
            Logger::info('Common options will not be processed.');
            return;
        }

        Logger::info('Processing common options.');
        foreach ($options->getEntries() as $key => $value) {
            $this->patterns->entry(
                $key,
                is_bool($value) ? '\2' : $value,
                comment: is_bool($value) && !$value
            );
        }
    }

    protected function processJIT(): void
    {
        $options = $this->jit;
        if ($options === null) {
            Logger::info('JIT will not be processed.');
            return;
        }

        Logger::info('Processing JIT.');
        $fullyDisable = !($options->getEnabled() || $options->getEnabledCli());
        $this->patterns->entry('zend_extension', '\2', 'opcache', $fullyDisable);
        $this->patterns->entry('opcache\.enable', '1', '\d', !$options->getEnabled());
        $this->patterns->entry('opcache\.enable_cli', '1', '\d', !$options->getEnabledCli());
        if ($fullyDisable) {
            Logger::info('JIT will be fully disabled!');
            return;
        }

        // See if flags/buffer-size entries already exist
        $toAdd = [];
        $ini = $this->readIni();
        if (str_contains($ini, 'opcache.jit')) {
            $this->patterns->entry('opcache\.jit', $options->getFlags());
            Logger::debug('Found already existing `opcache.jit`.');
        } else {
            $toAdd[] = "opcache.jit={$options->getFlags()}";
            Logger::debug('No `opcache.jit` found, proceeding to add.');
        }
        if (str_contains($ini, 'opcache.jit_buffer_size')) {
            $this->patterns->entry('opcache\.jit_buffer_size', $options->getBufferSize());
            Logger::debug('Found already existing `opcache.jit_buffer_size`.');
        } else {
            $toAdd[] = "opcache.jit_buffer_size={$options->getBufferSize()}";
            Logger::debug('No `opcache.jit_buffer_size` found, proceeding to add.');
        }
        if (count($toAdd) !== 0) {
            $toAdd = implode('', array_prefix("\n\n", $toAdd));
            $this->patterns->entry('opcache\.enable_cli', "\\2$toAdd", '\d');
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
                    ->setEnabledCli();
            }
        }
        $this->jit = $jit;
        return $this;
    }
}
