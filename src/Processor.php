<?php declare(strict_types=1);

namespace EasyIni;

use EasyIni\Options\JitOptions;
use EasyIni\Options\CommonOptions;

final class Processor extends Ini
{
    private bool $__setup = false;
    private array $extensions = [];
    private array $disabledClasses = [];
    private array $disabledFunctions = [];
    private ?JitOptions $jit = null;
    private ?CommonOptions $common = null;
    private PatternPairs $patterns;

    public function __construct()
    {
        $this->patterns = new PatternPairs;
        // $this->detectFPM();
    }

    private function detectFPM()
    {
        $is_fpm_running = false;
        $output = [];

        if (self::IS_WIN) {
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
            Logger::error('Cannot setup more than once');
            return false;
        }
        if (!ErrorCounter::empty()) {
            exit(1);
        }
        Logger::info('Env mode: ' . ($this->dev ? 'development' : 'production'));
        Logger::info("Using '{$this->getIniPath(template: true)}' as template.");
        $this->process();
        $res = $this->writeIni(
            preg_replace(
                $this->patterns->get('lookups'),
                $this->patterns->get('replacements'),
                $this->readIni()
            )
        );
        $this->__setup = true;
        Logger::info('Done!');
        return $res;
    }

    private function process(): void
    {
        $this->processDisabled();
        $this->processExtensions();
        $this->processDev();
        $this->processCommon();
        $this->processJit();
    }

    private function processDisabled(): void
    {
        $fnCount = count($this->disabledFunctions);
        $classCount = count($this->disabledClasses);
        if ($fnCount !== 0) {
            Logger::info("Found $fnCount functions to disable.");
            $this->patterns->entry('disable_functions', implode(',', $this->disabledFunctions), '.*');
        }
        if ($classCount !== 0) {
            Logger::info("Found $classCount classes to disable.");
            $this->patterns->entry('disable_classes', implode(',', $this->disabledClasses), '.*');
        }
    }

    private function processExtensions(): void
    {
        if (count($this->extensions) === 0) {
            Logger::info('No extension found!');
            return;
        }
        if (!self::IS_WIN) {
            Logger::notice('Extension handling is only supported on Windows. Skipping...');
            return;
        }
        $this->patterns->entry('extension_dir', prevValue: '"ext"');
        $this->patterns->entry('extension', prevValue: implode('|', $this->extensions));
        Logger::info('Found ' . count($this->extensions) . ' extensions.');
    }

    private function processDev(): void
    {
        if (!$this->dev) {
            return;
        }
        // Register `$argv`
        $this->patterns->entry('register_argc_argv', 'On');
        // Unlock PHAR editing
        $this->patterns->entry('phar\.readonly', 'Off');
    }

    private function processCommon(): void
    {
        $options = $this->common;
        if ($options === null) {
            Logger::info('Common options will not be processed.');
            return;
        }

        Logger::info('Processing common options.');
        foreach ($options->iterEntries() as $key => $value) {
            $this->patterns->entry(
                $key,
                is_bool($value) ? '\2' : $value,
                comment: is_bool($value) && !$value
            );
        }
    }

    private function processJit(): void
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
            Logger::notice('JIT will be fully disabled!');
            return;
        }

        // See if flags/buffer-size entries already exist
        $toAdd = [];
        $ini = $this->readIni();
        if (str_contains($ini, 'opcache.jit')) {
            $this->patterns->entry('opcache\.jit', $options->getFlags());
        } else {
            $toAdd[] = "opcache.jit={$options->getFlags()}";
            Logger::notice('No `opcache.jit` entry found, proceeding to add.');
        }
        if (str_contains($ini, 'opcache.jit_buffer_size')) {
            $this->patterns->entry('opcache\.jit_buffer_size', $options->getBufferSize());
        } else {
            $toAdd[] = "opcache.jit_buffer_size={$options->getBufferSize()}";
            Logger::notice('No `opcache.jit_buffer_size` entry found, proceeding to add.');
        }
        if (count($toAdd) !== 0) {
            $toAdd = implode('', array_prefix("\n\n", $toAdd));
            $this->patterns->entry('opcache\.enable_cli', "\\2$toAdd", '\d');
        }
    }

    public function setDisabledClasses(string ...$classes): self
    {
        $temp = array_unique(array_filter($classes));
        foreach ($temp as $i => $class) {
            if (class_exists($class))
                continue;

            Logger::warning("Class '$class' does not exist and will be ignored!");
            unset($temp[$i]);
        }
        $this->disabledClasses = $temp;
        return $this;
    }

    public function setDisabledFunctions(string ...$functions): self
    {
        $temp = array_unique(array_filter($functions));
        foreach ($temp as $i => $fn) {
            if (function_exists($fn))
                continue;

            Logger::warning("Function '$fn' does not exist and will be ignored!");
            unset($temp[$i]);
        }
        $this->disabledFunctions = $temp;
        return $this;
    }

    public function setExtensions(string ...$extensions): self
    {
        $this->extensions = array_unique(array_map('strtolower', array_filter($extensions)));
        return $this;
    }
    public function addExtension(string $ext): self
    {
        if ($ext !== '' && !in_array($ext = strtolower($ext), $this->extensions, true)) {
            $this->extensions[] = $ext;
        }
        return $this;
    }

    public function setCommon(CommonOptions $options): self
    {
        $this->common = $options;
        return $this;
    }

    public function setJit(JitOptions|bool $jit = true): self
    {
        if (is_bool($jit)) {
            $tmp = $jit;
            $jit = new JitOptions;
            if ($tmp === true) {
                $jit->setEnabled()
                    ->setEnabledCli();
            }
        }
        $this->jit = $jit;
        return $this;
    }
}
