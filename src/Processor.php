<?php declare(strict_types=1);

namespace EasyIni;

use EasyIni\Options\JitOptions;
use EasyIni\Options\ResourceLimitOptions;

use EasyIni\Processors\DevProcessor;
use EasyIni\Processors\JitProcessor;
use EasyIni\Processors\DisablingProcessor;
use EasyIni\Processors\ExtensionProcessor;
use EasyIni\Processors\ResourceLimitProcessor;

final class Processor extends Ini
{
    private bool $__setup = false;
    private array $extensions = [];
    private array $disabledClasses = [];
    private array $disabledFunctions = [];
    private ?JitOptions $jit = null;
    private ?ResourceLimitOptions $resourceLimits = null;

    /*
    public function __construct()
    {
        $this->detectFPM();
    }
    */

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

    public function setup(?string $inPath = null, ?string $outPath = null): bool
    {
        if ($this->__setup) {
            Logger::error('Cannot setup more than once');
            return false;
        }
        if (!ErrorCounter::empty()) {
            exit(1);
        }
        Logger::info('Env mode: ' . ($this->dev ? 'development' : 'production'));
        $res = $this->writeIni($this->process($inPath), $outPath);
        $this->__setup = true;
        Logger::info('Done!');
        return $res;
    }

    public function process(?string $inPath = null): string
    {
        $ini = $this->readIni($inPath);
        $patterns = new PatternPairs;
        $processors = [
            DisablingProcessor::class     => [
                'functions' => $this->disabledFunctions,
                'classes'   => $this->disabledClasses,
            ],
            ExtensionProcessor::class     => $this->extensions,
            DevProcessor::class           => $this->dev,
            ResourceLimitProcessor::class => $this->resourceLimits,
            JitProcessor::class           => $this->jit,
        ];
        foreach ($processors as $processor => $options) {
            $processor::process($ini, $patterns, $options);
        }
        return preg_replace(
            $patterns->getLookups(),
            $patterns->getReplacements(),
            $ini
        );
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

    public function setResourceLimits(ResourceLimitOptions $options): self
    {
        $this->resourceLimits = $options;
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
