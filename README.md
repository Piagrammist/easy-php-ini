# Easy php.ini

[![License: MIT](https://img.shields.io/badge/License-MIT-yellow.svg)](https://opensource.org/licenses/MIT)

A quick way to prepare your `php.ini` on Windows & Linux! ;-)

## Download

PowerShell:

```shell
iwr -outf setup-ini.php https://raw.githubusercontent.com/Piagrammist/easy-php-ini/main/scripts/setup-ini.php
```

Batch (Win 10+) & Other shells:

```shell
curl -o setup-ini.php https://raw.githubusercontent.com/Piagrammist/easy-php-ini/main/scripts/setup-ini.php
```

> [!TIP]
> For Windows 8.1 and below, you can manually download [curl.exe](https://curl.se/windows/).

## Usage

Simply execute the script using the target php binary:

```shell
C:\php\php.exe setup-ini.php
```

## Config

### Basic

Calling the `setup()` method will read, parse and write the ini. By default, it will only uncomment the `ext` entry on Windows:

```php
<?php

(new EasyIni\Processor)->setup();
```

### Extensions

Use the `setExtensions()` and/or `addExtension()` methods to add the desired extensions:

```php
<?php

$ini = new EasyIni\Processor;
$ini->setExtensions('curl', 'mbstring')
    ->addExtension('zip');
$ini->setExtensions('ftp'); // will override the previous ones
```

> [!NOTE]
>
> - Extension handling is only supported on Windows!
> - Zend extensions are not currently supported!

### Environment

Switch between `development` and `production` modes: (Default: `dev`)

```php
<?php

$ini = new EasyIni\Processor;
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

On Windows, if no `php.ini` already exists, `php.ini-{development,production}` will be used as the template depending on the env value.

> [!NOTE]
> In the dev environment, the following will be set:
>
> - `register_argc_argv = On`
>
> - `phar.readonly = Off`

### Common Options

A list of frequently used options could be set using the `setCommon()`, which accepts a `CommonOptions` object:

```php
<?php

use EasyIni\Processor;
use EasyIni\Options\CommonOptions;

$ini = new Processor;
$ini->setCommon(
    (new CommonOptions)
        ->setMaxInputTime(false)  // comments out the entry to use the default
        ->setMaxExecutionTime(30)
        ->setMemoryLimit('256M')
);
```

### JIT

JIT compilation can be enabled and configured using the `setJit()` method, which accepts either a `boolean` or a `JitOptions` object:

```php
<?php

use EasyIni\Processor;
use EasyIni\Options\JitOptions;

$ini = new Processor;
$ini->setJit()
    ->setJit(false);
$ini->setJit(
    (new JitOptions)
        ->setEnabled()
        ->setEnabledCli(false)
        ->setBufferSize('256M')
        ->setBufferSize(268_435_456) // =256M
        ->setFlags('tracing')
        ->setFlags(1254) // ='tracing'
);
```

### Full example

```php
<?php

use EasyIni\Processor;
use EasyIni\Options\JitOptions;
use EasyIni\Options\CommonOptions;

(new Processor)
    ->production()
    ->setExtensions(
        'curl',
        'mbstring',
        'mysqli',
        'pdo_mysql',
        'pdo_sqlite',
        'sqlite3',
    )
    ->setCommon(
        (new CommonOptions)
            ->setMaxInputTime(30)
            ->setMaxExecutionTime(30)
            ->setMemoryLimit('256M')
    )
    ->setJit(
        (new JitOptions)
            ->setEnabled()
            ->setEnabledCli()
            ->setBufferSize('256M')
    )
    ->setup();
```

## Logging

The logger level could be changed anywhere in the program using:

```php
<?php

EasyIni\Logger::setLevel(Monolog\Level::Debug);
```

## TODO

- [x] Add CI.
- [x] Add exception handling.
- [x] Add Linux support.
- [x] Add Logging.
- [x] Expand project into files and release PHAR.
- [ ] Add Mac OS support.
- [ ] Add tests.
- [ ] Add dependabot for dependency check updates.
- [ ] Automate PHAR release using CD.
