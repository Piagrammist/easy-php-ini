<?php declare(strict_types=1);

$phar = __DIR__ . '/easy-ini.phar';
if (!is_file($phar)) {
    copy(
        'https://github.com/Piagrammist/easy-php-ini/releases/download/v0.1/easy-ini.phar',
        $phar
    );
}
require $phar;

(new EasyIni\Parser)
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
