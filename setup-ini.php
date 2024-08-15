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

function setup(array $extensions = [], bool $dev = true): int|false
{
    pattern('~;(extension_dir) *= *"(ext)"~i', '\1 = "\2"');

    if ($dev) {
        // Register `$argv`
        pattern('~;?(register_argc_argv) *= *Off~i', '\1 = On');

        // Unlock PHAR editing
        pattern('~;?(phar\.readonly) *= *On~i', '\1 = Off');
    }

    if (!empty($extensions)) {
        pattern(
            '~;(extension) *= *(' . implode('|', $extensions) . ')~i',
            '\1=\2'
        );
    }

    $patterns = pattern();
    $newIni = preg_replace(
        $patterns['lookup'],
        $patterns['replace'],
        file_get_contents(getIniPath($dev))
    );
    return file_put_contents(
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

function pattern(?string $lookup = null, ?string $replacement = null): array
{
    static $patterns = array(
        'lookup' => [],
        'replace' => [],
    );

    if ($lookup && $replacement) {
        $patterns['lookup'][] = $lookup;
        $patterns['replace'][] = $replacement;
    }
    return $patterns;
}

function path(string ...$parts): string
{
    return implode(DIRECTORY_SEPARATOR, $parts);
}
