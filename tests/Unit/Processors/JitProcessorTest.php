<?php

use EasyIni\Ini\EntryState;
use EasyIni\Options\JitOptions;
use EasyIni\Processors\JitProcessor;

it('must make the specified output', function () {
    $input = <<<'EOI'
        ;extension=zip

        ;zend_extension=opcache

        [opcache]
        ; Determines if Zend OPCache is enabled
        ;opcache.enable=1

        ; Determines if Zend OPCache is enabled for the CLI version of PHP
        ;opcache.enable_cli=0

        ; The OPcache shared memory storage size.
        ;opcache.memory_consumption=128
        EOI;

    $expected = <<<'EOI'
        ;extension=zip

        zend_extension=opcache

        [opcache]
        ; Determines if Zend OPCache is enabled
        opcache.enable = 0

        ; Determines if Zend OPCache is enabled for the CLI version of PHP
        ;opcache.enable_cli = 1

        opcache.jit = 1255

        ;opcache.jit_buffer_size = 64M

        ; The OPcache shared memory storage size.
        ;opcache.memory_consumption=128
        EOI;

    $options = (new JitOptions)
        ->setEnabled(false)
        ->setEnabledCli(true, EntryState::COMMENT)
        ->setFlags(1255)
        ->setBufferSize(state: EntryState::COMMENT);

    $this->performProcessorTest(
        JitProcessor::class,
        $options,
        $input,
        $expected,
    );
});
