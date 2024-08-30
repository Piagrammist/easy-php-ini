<div align="center">
    <h1>Easy php.ini</h1>
    <p>A quick way to prepare your php.ini! ;-)</p>
    <a href="https://opensource.org/licenses/MIT">
        <img src="https://img.shields.io/badge/License-MIT-yellow.svg" alt="License: MIT">
    </a>
</div>

## Table of Contents

- [Download](#download)
- [Usage](#usage)
- [Config](#config)
    - [Environment](#environment)
    - [Extensions](#extensions)
    - [Resource Limiting](#resource-limiting)
    - [Disabling Functions and Classes](#disabling-functions-and-classes)
    - [Just In Time Compilation](#just-in-time-compilation)
    - [Full example](#full-example)
- [Logging](#logging)
- [TODO](#todo)

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

Calling the `setup()` method will read, process and write the ini. By default, nothing happens!

```php
<?php

use EasyIni\Processor;

$ini = new Processor;
$ini->setup();

/*
 * The needed paths will be automatically detected,
 *   but if you "really" needed to specify custom input/output php.ini paths,
 *   you can do so:
 */
$ini->setup(
    '/home/rz/input.ini',
    '/home/rz/output.ini',
);
```

### Environment

Switch between `development` and `production` modes: (Default is `dev`)

```php
<?php

$ini->development()
    ->production(); // overrides the previous

/*
 * allowed params for `env()`:
 *   d,  dev, development
 *   p, prod, production
 */
$ini->env('dev');

// switches to `production` mode
$ini->development(false);
```

On Windows, if no `php.ini` already exists, `php.ini-{development,production}` will be used as the template depending on the env value.

> [!NOTE]
> In the dev environment, the following will be set:
>
> - `register_argc_argv = On`
>
> - `phar.readonly = Off`

### Extensions

Use the `setExtensions()` and/or `addExtension()` methods to add the desired extensions:

```php
<?php

$ini->setExtensions('curl', 'mbstring')
    ->addExtension('zip');

// will override the previous ones
$ini->setExtensions('ftp');
```

> [!NOTE]
> Extension handling is only supported on Windows!
>
> And if any extension provided, the `extension_dir` entry will be automatically uncommented.

### Resource Limiting

Resource limiting options could be set by calling `setResourceLimits()`, which accepts a `ResourceLimitOptions` object:

```php
<?php

use EasyIni\Ini\EntryState;
use EasyIni\Options\ResourceLimitOptions;

$limits = new ResourceLimitOptions;
// comments out the entry to use the default
$limits->setMaxExecutionTime(state: EntryState::COMMENT);
$limits
    ->setMaxInputTime(30)
    ->setMaxInputVars(100)
    ->setMemoryLimit('256M');

$ini->setResourceLimits($limits);
```

### Disabling Functions and Classes

Internal php functions/classes can be disabled by calling the `setDisabledXxx()` methods:

```php
<?php

$ini->setDisabledFunctions('exec', 'shell_exec');

// Warning: `a` is not a class and will be ignored.
$ini->setDisabledClasses('ZipArchive', 'a');
```

### Just In Time Compilation

JIT compilation can be enabled and configured using the `setJit()` method, which accepts either a `boolean` or a `JitOptions` object:

```php
<?php

use EasyIni\Ini\EntryState;
use EasyIni\Options\JitOptions;

$ini->setJit();
$ini->setJit(false);

$jit = new JitOptions;
$jit
    ->setEnabled(false, EntryState::COMMENT)
    ->setEnabledCli();
$jit
    ->setBufferSize('64M') // default
    ->setBufferSize(67_108_864); // same as '64M'
$jit
    ->setFlags('tracing') // default
    ->setFlags(1254); // same as 'tracing'

$ini->setJit($jit);
```

### Full example

```php
<?php

use EasyIni\Processor;
use EasyIni\Options\JitOptions;
use EasyIni\Options\ResourceLimitOptions;

(new Processor)
    ->production()
    ->setDisabledFunctions('exec', 'shell_exec')
    ->setExtensions(
        'curl',
        'mbstring',
        'mysqli',
        'pdo_mysql',
        'pdo_sqlite',
        'sqlite3',
    )
    ->setResourceLimits(
        (new ResourceLimitOptions)
            ->setMaxInputTime(30)
            ->setMemoryLimit('256M')
    )
    ->setJit(
        (new JitOptions)
            ->setEnabled()
            ->setEnabledCli(false)
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

- [x] Add tests.
- [x] Add CI.
- [x] Add exception handling.
- [x] Add Linux support.
- [x] Add Logging.
- [x] Expand project into files and release PHAR.
- [ ] Add dependabot for dependency update checks.
- [ ] Automate PHAR release using CD.
