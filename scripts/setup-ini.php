<?php

use EasyIni\Processor;
use EasyIni\Options\ExtensionOptions;
use EasyIni\Options\ResourceLimitOptions;

if (is_file($loader = dirname(__DIR__) . '/vendor/autoload.php')) {
    require $loader;
} else {
    if (!is_file($phar = __DIR__ . '/easy-ini.phar')) {
        copy(
            'https://github.com/Piagrammist/easy-php-ini/releases/latest/download/easy-ini.phar',
            $phar,
        );
    }
    require $phar;
}

(new Processor)
    ->development()
    ->setExtension(
        (new ExtensionOptions)
            ->setExtensions([
                'curl',
                'mbstring',
                'mysqli',
                'openssl',
                'pdo_mysql',
                'pdo_sqlite',
                'sqlite3',
                'sockets',
                'zip',
            ])
    )
    ->setResourceLimits(
        (new ResourceLimitOptions)
            ->setMemoryLimit('512M')
    )
    ->setJit()
    ->setup();
