# Easy php.ini

[![License: MIT](https://img.shields.io/badge/License-MIT-yellow.svg)](https://opensource.org/licenses/MIT)

A quick way to prepare your `php.ini` on Windows! ;-)

## Download

PowerShell:

```shell
iwr -outf setup-ini.php https://raw.githubusercontent.com/Piagrammist/easy-php-ini/main/setup-ini.php
```

Batch (Win 10+):

```shell
curl -o setup-ini.php https://raw.githubusercontent.com/Piagrammist/easy-php-ini/main/setup-ini.php
```

> [!TIP]
> For Windows 8.1 and below, you can manually download [curl.exe](https://curl.se/windows/).

## Usage

Simply execute the script using the target php binary:

```shell
C:\php\php.exe setup-ini.php
```

This will automatically find the right ini file, edit and write it to `php.ini` at the php bin directory.

## Config

### Basic

Calling the `setup()` method will read, parse and write the ini. By default, it will only uncomment the `ext` entry:

```php
<?php

(new EasyIni)->setup();
```

### Extensions

Use the `setExtensions()` and/or `addExtension()` methods to add the desired extensions:

```php
<?php

$ini = new EasyIni;
$ini->setExtensions('curl', 'mbstring')
    ->addExtension('zip');
$ini->setExtensions('ftp'); // will override the previous ones
```

> [!NOTE]
> Zend extensions are not currently supported!

### Environment

Switch between `development` and `production` modes: (Default: `dev`)

```php
<?php

$ini = new EasyIni;
$ini->development()
    ->production(); // overrides the previous
/*
 * allowed params for `env()`:
 *   d,  dev, development
 *   p, prod, production
 */
$ini->env('dev');
$ini->development(false); // switches to `production` mode
```

If no `php.ini` already exists, `php.ini-{development,production}` will be used as the template depending on the env value.

> [!NOTE]
> In the dev environment, the following will be set:
>
> -   `register_argc_argv = On`
>
> -   `phar.readonly = Off`

### JIT

JIT compilation can be enabled and configured using the `setJIT()` method, which accepts either a `boolean` or a `JITOptions` object:

```php
<?php

$ini = new EasyIni;
$ini->setJIT()
    ->setJIT(false);
$ini->setJIT(
    (new JITOptions)
        ->setEnabled()
        ->setEnabledCLI(false)
        ->setBufferSize('256M')
        ->setBufferSize(268_435_456) // =256M
        ->setFlags('tracing')
        ->setFlags(1254) // ='tracing'
);
```

### Full example

```php
<?php

(new EasyIni)
    ->production()
    ->setExtensions(
        'curl',
        'mbstring',
        'mysqli',
        'pdo_mysql',
        'pdo_sqlite',
        'sqlite3',
    )
    ->setJIT(
        (new JITOptions)
            ->setEnabled()
            ->setEnabledCLI()
            ->setBufferSize('256M')
    )
    ->setup();
```

## TODO

-   [ ] Expand project into files and release PHAR.
