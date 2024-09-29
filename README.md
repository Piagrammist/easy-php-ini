<div align="center">
    <h1>Easy php.ini</h1>
    <p>A quick way to prepare your php.ini! ;-)</p>
    <a href="https://opensource.org/licenses/MIT">
        <img src="https://img.shields.io/github/license/Piagrammist/easy-php-ini?color=yellow" alt="License">
    </a>
    <a href="https://github.com/Piagrammist/easy-php-ini/actions/workflows/CI.yml">
        <img src="https://img.shields.io/github/actions/workflow/status/Piagrammist/easy-php-ini/CI.yml?event=push" alt="CI status">
    </a>
    <a href="https://www.php.net/downloads">
        <img src="https://img.shields.io/badge/php-%3D%3E8.2-8892bf" alt="Min PHP version">
    </a>
</div>

## Table of Contents

- [Usage](#usage)
- [Config](#config)
  - [Environment](#environment)
  - [Extensions](#extensions)
  - [Error Handling](#error-handling)
  - [Resource Limits](#resource-limits)
  - [Disable Functions and Classes](#disable-functions-and-classes)
  - [Just In Time Compilation](#just-in-time-compilation)
  - [Full example](#full-example)
- [Logging](#logging)
- [TODO](#todo)

## Usage

- Download the template script:

    ```shell
    curl -o setup-ini.php https://raw.githubusercontent.com/Piagrammist/easy-php-ini/main/scripts/setup-ini.php
    ```

- Execute the script using the target php binary:

    ```shell
    C:\php\php.exe setup-ini.php
    ```

> [!TIP]
> For Windows 8.1 and below, you can manually download [curl.exe](https://curl.se/windows/).

## Config

Calling the `setup()` method will read, process and write the ini.
`process()` can be used instead if you do not wish to output to a file.

By default nothing happens!

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

$output = $ini->process('/home/rz/input.ini');
```

### Environment

This is only useful for **Windows users**.
If no `php.ini` already exists, `php.ini-{development,production}` will be
used as the template depending on the env value.

**Default** is set to `development`.

```php
<?php

$ini->development()
    ->production(); // overrides the previous

// switches to `production` mode
$ini->development(false);
```

### Extensions

Extension handling is only supported on **Windows**!

```php
<?php

use EasyIni\Ini\EntryState;
use EasyIni\Options\ExtensionOptions;

$extension = new ExtensionOptions;
$extension->setExtensions([
    'curl',
    'ftp',
    'mysqli',
    'zip',
]);

/*
 * Comment/disables the extensions.
 *
 * Note that this overrides the previous.
 *   You'd need to make a separate processor-
 *   if you need to add and remove extensions at the same time!
 */
$extension->setExtensions([
    'curl',
    'ftp',
    'mysqli',
    'zip',
], EntryState::COMMENT);

// A custom `ext dir` could be set, but is not necessary!
$extension->setExtensionDir('C:\my\custom\extension\path');

$ini->setExtension($extension);
```

> [!TIP]
> If any extension provided, the `extension_dir` entry will be automatically uncommented.

### Error Handling

```php
<?php

use EasyIni\Options\ErrorHandlingOptions;

$errorHandling = new ErrorHandlingOptions;
$errorHandling
    ->setHtmlErrors()
    ->setDisplayErrors()
    ->setDisplayStartupErrors()
    ->setLogErrors(false);

$ini->setErrorHandling($errorHandling);
```

### Resource Limits

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

### Disable Functions and Classes

Internal php functions/classes can be disabled by providing a `DisableOptions`:

```php
<?php

use EasyIni\Options\DisableOptions;

$disable = new DisableOptions;
$disable->setFunctions(['exec', 'shell_exec']);
// WARN: `a` is not a class and will be ignored. (strict mode)
$disable->setClasses(['ZipArchive', 'a']);

// The strict behavior can be disabled using:
$disable->setStrict(false);

$ini->setDisable($disable);
```

### Just In Time Compilation

JIT compilation can be enabled and configured using the `setJit()` method,
which accepts either a `boolean` or `JitOptions` object:

```php
<?php

use EasyIni\Ini\EntryState;
use EasyIni\Options\JitOptions;

$jit = new JitOptions;

$jit->setEnabled(false, EntryState::COMMENT);
$jit->setEnabledCli();

$jit->setFlags('tracing'); // default
$jit->setFlags(1254); // same as 'tracing'

$jit->setBufferSize('64M'); // default
$jit->setBufferSize(67_108_864); // same as '64M'

$ini->setJit($jit);

// --------
// Or the quick way:
$ini->setJit();
$ini->setJit(false);
```

### Full example

```php
<?php

use EasyIni\Processor;
use EasyIni\Options\JitOptions;
use EasyIni\Options\DisableOptions;
use EasyIni\Options\ExtensionOptions;
use EasyIni\Options\ErrorHandlingOptions;
use EasyIni\Options\ResourceLimitOptions;

(new Processor)
    ->production()
    ->setDisable(
        (new DisableOptions)
            ->setFunctions(['exec', 'shell_exec'])
    )
    ->setExtension(
        (new ExtensionOptions)
            ->setExtensions([
                'curl',
                'mbstring',
                'mysqli',
                'pdo_mysql',
                'pdo_sqlite',
                'sqlite3',
            ])
    )
    ->setErrorHandling(
        (new ErrorHandlingOptions)
            ->setDisplayErrors(false)
            ->setDisplayStartupErrors(false)
            ->setLogErrors()
            ->setLogFile('php.log')
    )
    ->setResourceLimit(
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
- [ ] Add `error_reporting` entry to `ErrorHandlingProcessor`.
