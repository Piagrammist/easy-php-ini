<?php declare(strict_types=1);

namespace EasyIni;

class Parser extends Ini
{
    private bool $__setup = false;
    protected array $extensions = [];
    protected ?JITOptions $jit = null;
    protected PatternPairs $patterns;

    public function __construct()
    {
        parent::__construct(true);

        $this->patterns = new PatternPairs;
        $this->patterns->set('ext_dir', '~;(extension_dir) *= *"(ext)"~', '\1 = "\2"');
    }

    public function setup(): bool
    {
        if ($this->__setup) {
            throw new \BadMethodCallException('Cannot setup more than once');
        }

        $this->parse();
        $this->__setup = true;
        return self::writeIni(
            preg_replace(
                $this->patterns->get('lookups'),
                $this->patterns->get('replacements'),
                $this->readIni()
            )
        );
    }

    protected function parse(): void
    {
        $this->processExtensions();
        $this->processDevOptions();
        $this->processJIT();
    }

    protected function processExtensions(): void
    {
        if (count($this->extensions) === 0) {
            return;
        }
        $this->patterns->set(
            'extensions',
            '~;(extension) *= *(' . implode('|', $this->extensions) . ')~',
            '\1=\2'
        );
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

    protected function processJIT(): void
    {
        $jit = $this->jit;
        if ($jit === null) {
            return;
        }

        $fullyDisable = !($jit->getEnabled() || $jit->getEnabledCLI());
        $this->patterns->set(
            'opcache',
            '~;?(zend_extension) *= *(opcache)~',
            self::comment($fullyDisable) . '\1=\2'
        );
        $this->patterns->set(
            'opcache_enable',
            '~;?(opcache\.enable) *= *\d~',
            self::comment($jit->getEnabled()) . '\1=1'
        );
        $this->patterns->set(
            'opcache_enable_cli',
            '~;?(opcache\.enable_cli) *= *\d~',
            self::comment($jit->getEnabledCLI()) . '\1=1'
        );
        if ($fullyDisable) {
            return;
        }

        // See if flags/buffer-size entries already exist
        $toAdd = [];
        $ini = $this->readIni();
        if (str_contains($ini, 'opcache.jit')) {
            $this->patterns->set('jit', '~;?(opcache\.jit) *= *.+~', "\\1={$jit->getFlags()}");
        } else {
            $toAdd[] = "opcache.jit={$jit->getFlags()}";
        }
        if (str_contains($ini, 'opcache.jit_buffer_size')) {
            $this->patterns->set(
                'jit_bugger_size',
                '~;?(opcache\.jit_buffer_size) *= *.+~',
                "\\1={$jit->getBufferSize()}"
            );
        } else {
            $toAdd[] = "opcache.jit_buffer_size={$jit->getBufferSize()}";
        }
        if (count($toAdd) !== 0) {
            $toAdd = implode('', array_prefix("\n\n", $toAdd));
            $this->patterns->set('jit_entries', '~(;?opcache\.enable_cli) *= *(\d)~', '\1=\2' . $toAdd);
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
