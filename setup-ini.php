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

    public function add(string $lookup, string $replacement): static
    {
        if (!empty($lookup)) {
            $this->lookups[] = $lookup;
            $this->replacements[] = $replacement;
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

class Environment
{
    public function __construct(
        protected bool $dev,
    ) {}

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
    protected function writeIni(string $content): bool
    {
        return (bool)file_put_contents(
            path(PHP_BINDIR, 'php.ini'),
            $content
        );
    }
}

class EasyIni extends Ini
{
    private bool $__setup = false;
    protected PatternPairs $patterns;
    protected array $extensions = [];

    public function __construct(bool $dev = true)
    {
        parent::__construct($dev);

        $this->patterns = new PatternPairs;
        $this->patterns->add('~;(extension_dir) *= *"(ext)"~', '\1 = "\2"');
    }

    public function setup(): bool
    {
        if ($this->__setup) {
            throw new BadMethodCallException('Cannot setup more than once');
        }

        $this->parse();
        $this->__setup = true;
        return $this->writeIni(
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
    }

    protected function processExtensions(): void
    {
        if (count($this->extensions) === 0) {
            return;
        }
        $this->patterns->add(
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
        $this->patterns->add('~;?(register_argc_argv) *= *Off~', '\1 = On');

        // Unlock PHAR editing
        $this->patterns->add('~;?(phar\.readonly) *= *On~', '\1 = Off');
    }

    public function setExtensions(string ...$extensions): static
    {
        $this->extensions = array_unique(array_map('strtolower', array_filter($extensions)));
        return $this;
    }
    public function addExtension(string $ext): static
    {
        if ($ext !== '' && array_search($ext = strtolower($ext), $this->extensions, true) === false) {
            $this->extensions[] = $ext;
        }
        return $this;
    }
}

function path(string ...$parts): string
{
    return implode(DIRECTORY_SEPARATOR, $parts);
}