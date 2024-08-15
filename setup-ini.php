<?php

declare(strict_types=1);

setup([
    'curl',
    'mbstring',
    'mysqli',
    'openssl',
    'pdo_mysql',
    'pdo_sqlite',
    'sqlite3',
    'sockets',
    'zip',
]);

function setup(array $extensions = [], bool $dev = true): bool
{
    $patterns = new PatternPairs;
    $patterns->add('~;(extension_dir) *= *"(ext)"~i', '\1 = "\2"');

    if (!empty($extensions)) {
        $patterns->add(
            '~;(extension) *= *(' . implode('|', $extensions) . ')~i',
            '\1=\2'
        );
    }

    if ($dev) {
        // Register `$argv`
        $patterns->add('~;?(register_argc_argv) *= *Off~i', '\1 = On');

        // Unlock PHAR editing
        $patterns->add('~;?(phar\.readonly) *= *On~i', '\1 = Off');
    }

    $newIni = preg_replace(
        $patterns->get('lookups'),
        $patterns->get('replacements'),
        file_get_contents(getIniPath($dev))
    );
    return (bool)file_put_contents(
        path(PHP_BINDIR, 'php.ini'),
        $newIni
    );
}

function getIniPath(bool $dev = true): string
{
    $p = path(PHP_BINDIR, 'php.ini');
    if (is_file($p)) {
        return $p;
    }

    $p .= $dev ? '-development' : '-production';
    if (is_file($p)) {
        return $p;
    }

    throw new RuntimeException("ini does not exist @ '$p'");
}

function path(string ...$parts): string
{
    return implode(DIRECTORY_SEPARATOR, $parts);
}

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
