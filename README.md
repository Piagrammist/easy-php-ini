# Easy php.ini

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

This will automatically find the right [default] ini file, edit and write it to `php.ini` at the php bin directory.

## Config

### Basic

Calling the `setup()` function w/o any parameters will only uncomment the `ext` entry in `php.ini`:

```php
<?php

setup();
```

### Extensions

`setup()` accepts an array of extensions as the first parameter:

```php
<?php

setup(['curl', 'mbstring']);
```

> [!NOTE]
> Zend extensions are not currently supported!

### Environment

Second parameter will determine the `development` or `production` mode:

```php
<?php

setup([], true);
setup(dev: true); // does the same
```

If no `php.ini` already exists, `php.ini-development` or `php.ini-production` will be used as the template depending on the value of the `dev` parameter. `Development` mode is the default!
