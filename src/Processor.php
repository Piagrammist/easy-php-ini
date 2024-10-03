<?php declare(strict_types=1);

namespace EasyIni;

use EasyIni\Options\JitOptions;
use EasyIni\Options\PharOptions;
use EasyIni\Options\DisableOptions;
use EasyIni\Options\ExtensionOptions;
use EasyIni\Options\ErrorHandlingOptions;
use EasyIni\Options\ResourceLimitOptions;

use EasyIni\Processors\JitProcessor;
use EasyIni\Processors\PharProcessor;
use EasyIni\Processors\DisableProcessor;
use EasyIni\Processors\ExtensionProcessor;
use EasyIni\Processors\ErrorHandlingProcessor;
use EasyIni\Processors\ResourceLimitProcessor;

final class Processor extends Ini
{
    private bool $__setup = false;
    private ?JitOptions $jit = null;
    private ?PharOptions $phar = null;
    private ?DisableOptions $disable = null;
    private ?ExtensionOptions $extension = null;
    private ?ErrorHandlingOptions $errorHandling = null;
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

        if (IS_WIN) {
            // Check for php-fpm on Windows
            \exec('tasklist /FI "IMAGENAME eq php-fpm.exe"', $output);
            \exec('tasklist /FI "IMAGENAME eq php-cgi.exe"', $output);
        } else {
            // Check for php-fpm on Unix/Linux for multiple versions
            \exec('ps aux | grep -E "php[0-9.]*-fpm|php-fpm" | grep -v grep', $output);
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
            Logger::error(Lang::get('err_setup'));
            return false;
        }
        Logger::info(Lang::get('env_mode', $this->dev ? 'development' : 'production'));
        $res = $this->writeIni($this->process($inPath), $outPath);
        $this->__setup = true;
        Logger::info(Lang::get('done'));
        return $res;
    }

    public function process(?string $inPath = null): string
    {
        if (!ErrorCounter::empty()) {
            exit(1);
        }
        $ini = $this->readIni($inPath);
        $processors = [
            JitProcessor::class           => $this->jit,
            PharProcessor::class          => $this->phar,
            DisableProcessor::class       => $this->disable,
            ExtensionProcessor::class     => $this->extension,
            ErrorHandlingProcessor::class => $this->errorHandling,
            ResourceLimitProcessor::class => $this->resourceLimits,
        ];
        foreach ($processors as $class => $options) {
            $processor = new $class($ini);
            $processor->apply($options);
            $ini = $processor->replace();
        }
        return $ini;
    }

    public function setPhar(PharOptions $options): self
    {
        $this->phar = $options;
        return $this;
    }
    public function setDisable(DisableOptions $options): self
    {
        $this->disable = $options;
        return $this;
    }
    public function setExtension(ExtensionOptions $options): self
    {
        $this->extension = $options;
        return $this;
    }
    public function setErrorHandling(ErrorHandlingOptions $options): self
    {
        $this->errorHandling = $options;
        return $this;
    }
    public function setResourceLimits(ResourceLimitOptions $options): self
    {
        $this->resourceLimits = $options;
        return $this;
    }
    public function setJit(JitOptions|bool $options = true): self
    {
        if (\is_bool($options)) {
            $temp = $options;
            $options = new JitOptions;
            if ($temp) {
                $options->setEnabled();
                $options->setEnabledCli();
            }
        }
        $this->jit = $options;
        return $this;
    }
}
