<?php

use EasyIni\Ini\EntryState;
use EasyIni\Options\ErrorHandlingOptions;
use EasyIni\Processors\ErrorHandlingProcessor;

it('must make the specified output', function () {
    $input = <<<'EOI'
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
        log_errors = Off

        ; Note: This directive is hardcoded to Off for the CLI SAPI
        ;html_errors = On

        ; Log errors to specified file. PHP's default behavior is to leave this
        ; value empty.
        ; Example:
        error_log =
        ; Log errors to syslog (Event Log on Windows).
        ;error_log = syslog
        EOI;

    $options = (new ErrorHandlingOptions)
        ->setDisplayErrors(false)
        ->setDisplayStartupErrors(false, state: EntryState::COMMENT)
        ->setLogFile('');

    $this->performProcessorTest(
        ErrorHandlingProcessor::class,
        $options,
        $input,
        $expected,
    );
});
