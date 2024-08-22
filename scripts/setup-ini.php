<?php declare(strict_types=1);

use EasyIni\Processor;
use EasyIni\CommonOptions;

if (is_file($loader = __DIR__ . '/../vendor/autoload.php')) {
    require $loader;
} else {
    if (!is_file($phar = __DIR__ . '/easy-ini.phar')) {
        copy(
            'https://github.com/Piagrammist/easy-php-ini/releases/download/v0.2/easy-ini.phar',
            $phar
        );
    }
    require $phar;
}

(new Processor)
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
    ->setCommon(
        (new CommonOptions)
            ->setMemoryLimit('512M')
    )
    ->setJIT()
    ->setup();
