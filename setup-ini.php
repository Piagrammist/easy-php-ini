<?php

declare(strict_types=1);

(new EasyIni)
    ->development()
    ->setExtensions(
        'curl',
        'mbstring',
        'mysqli',
        'openssl',
        'pdo_mysql',
        'pdo_sqlite',
        'sqlite3',
        'sockets',
        'zip',
    )
    ->setup();

class PatternPairs
{
    protected array $lookups = [];
    protected array $replacements = [];

    public function set(string $index, string $lookup, string $replacement): static
    {
        if (!($lookup === '' || $index === '' || isset($this->lookups[$index]))) {
            $this->lookups[$index] = $lookup;
            $this->replacements[$index] = $replacement;
        }
        return $this;
    }

    public function get(string $key): ?array
    {
        return match ($key) {
            'lookups' => $this->lookups,
            'replacements' => $this->replacements,
            default => null,
        };
    }
}

class JITOptions
{
    protected bool $enabled = false;
    protected bool $enabledCLI = false;
    protected string|int $flags = 'tracing';
    protected string|int $bufferSize = '64M';

    protected static array $allowedStringFlags = [
        'disable',
        'on',
        'off',
        'tracing',
        'function',
    ];

    public function setEnabled(bool $enable = true): static
    {
        $this->enabled = $enable;
        return $this;
    }
    public function getEnabled(): bool
    {
        return $this->enabled;
    }

    public function setEnabledCLI(bool $enable = true): static
    {
        $this->enabledCLI = $enable;
        return $this;
    }
    public function getEnabledCLI(): bool
    {
        return $this->enabledCLI;
    }

    public function setFlags(string|int $flags): static
    {
        if (!((is_int($flags) && digitCount($flags) === 4) ||
            (is_string($flags) && in_array($flags = strtolower($flags), static::$allowedStringFlags, true)))) {
            throw new InvalidArgumentException('JIT flags must be a 4 digit number or ' .
                'one of "' . implode(', ', static::$allowedStringFlags) . '"');
        }
        $this->flags = $flags;
        return $this;
    }
    public function getFlags(): string|int
    {
        return $this->flags;
    }

    public function setBufferSize(string|int $size): static
    {
        if (!preg_match('~^\d+[KMG]?$~i', $size)) {
            throw new InvalidArgumentException('JIT buffer size must be a positive value in bytes, ' .
                "or with standard PHP data size suffixes (K, M or G) e.g. '256M'");
        }
        $this->bufferSize = $size;
        return $this;
    }
    public function getBufferSize(): string|int
    {
        return $this->bufferSize;
    }
}

class Environment
{
    public function __construct(protected bool $dev) {}

    public function development(bool $dev = true): static
    {
        $this->dev = $dev;
        return $this;
    }
    public function production(bool $prod = true): static
    {
        $this->dev = !$prod;
        return $this;
    }
    public function env(string $key): static
    {
        $dev = match (strtolower($key)) {
            'p', 'prod', 'production' => false,
            'd', 'dev', 'development' => true,
            default => null,
        };
        if ($dev === null) {
            throw new InvalidArgumentException('Wrong environment mode');
        }
        $this->dev = $dev;
        return $this;
    }
}

class Ini extends Environment
{
    public function getIniPath(): string
    {
        $p = path(PHP_BINDIR, 'php.ini');
        if (is_file($p)) {
            return $p;
        }

        $p .= $this->dev ? '-development' : '-production';
        if (is_file($p)) {
            return $p;
        }

        throw new RuntimeException("ini does not exist @ '$p'");
    }

    protected function readIni(): string
    {
        return file_get_contents($this->getIniPath());
    }
    protected static function writeIni(string $content): bool
    {
        return (bool)file_put_contents(
            path(PHP_BINDIR, 'php.ini'),
            $content
        );
    }

    public static function comment(bool $condition = true): string
    {
        return $condition ? ';' : '';
    }
}

class EasyIni extends Ini
{
    private bool $__setup = false;
    protected array $extensions = [];
    protected JITOptions $jit;
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
            throw new BadMethodCallException('Cannot setup more than once');
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

function array_prefix(string $prefix, array $array)
{
    return preg_filter('~^~', $prefix, $array);
}

function digitCount(int $number): int
{
    return $number !== 0 ? (int)(log10($number) + 1) : 1;
}

function path(string ...$parts): string
{
    return implode(DIRECTORY_SEPARATOR, $parts);
}
