<?php

use Monolog\Level;
use EasyIni\Logger;
use EasyIni\PatternPairs;
use EasyIni\Ini\EntryState;
use EasyIni\Options\ErrorHandlingOptions;
use EasyIni\Processors\ErrorHandlingProcessor;

it('must make the specified output', function () {
    Logger::setLevel(Level::Emergency);

    $input = <<<'EOI'
        ; Default Value: E_ALL
        ; Development Value: E_ALL
        ; Production Value: E_ALL & ~E_DEPRECATED & ~E_STRICT
        error_reporting = E_ALL & ~E_DEPRECATED & ~E_STRICT

        ; Default Value: On
        ; Development Value: On
        ; Production Value: Off
        display_errors = On

        ; Default Value: On
        ; Development Value: On
        ; Production Value: Off
        display_startup_errors = On

        ; Default Value: Off
        ; Development Value: On
        ; Production Value: On
        ;log_errors = Off

        ; Note: This directive is hardcoded to Off for the CLI SAPI
        ;html_errors = On

        ; Log errors to specified file. PHP's default behavior is to leave this
        ; value empty.
        ; Example:
        ;error_log = php_errors.log
        ; Log errors to syslog (Event Log on Windows).
        ;error_log = syslog
        EOI;

    $expected = <<<'EOI'
        ; Default Value: E_ALL
        ; Development Value: E_ALL
        ; Production Value: E_ALL & ~E_DEPRECATED & ~E_STRICT
        error_reporting = E_ALL & ~E_DEPRECATED & ~E_STRICT

        ; Default Value: On
        ; Development Value: On
        ; Production Value: Off
        display_errors = Off

        ; Default Value: On
        ; Development Value: On
        ; Production Value: Off
        ;display_startup_errors = Off

        ; Default Value: Off
        ; Development Value: On
        ; Production Value: On
        log_errors = On

        ; Note: This directive is hardcoded to Off for the CLI SAPI
        ;html_errors = On

        ; Log errors to specified file. PHP's default behavior is to leave this
        ; value empty.
        ; Example:
        error_log = C:\php-log.php
        ; Log errors to syslog (Event Log on Windows).
        ;error_log = syslog
        EOI;

    $patterns = new PatternPairs;
    $options = (new ErrorHandlingOptions)
        ->setDisplayErrors(false)
        ->setDisplayStartupErrors(false, state: EntryState::COMMENT)
        ->setLogErrors()
        ->setLogFile('C:\php-log.php');

    ErrorHandlingProcessor::process($input, $patterns, $options);
    $output = preg_replace(
        $patterns->getLookups(),
        $patterns->getReplacements(),
        $input,
        1,
    );

    expect(trimCR($output))->toBe(trimCR($expected));
});
