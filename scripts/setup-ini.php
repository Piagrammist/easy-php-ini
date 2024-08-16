<?php declare(strict_types=1);

if (is_file($loader = __DIR__ . '/../vendor/autoload.php')) {
    require $loader;
} else {
    if (!is_file($phar = __DIR__ . '/easy-ini.phar')) {
        copy(
            'https://github.com/Piagrammist/easy-php-ini/releases/download/v0.1/easy-ini.phar',
            $phar
        );
    }
    require $phar;
}

(new EasyIni\Processor)
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
