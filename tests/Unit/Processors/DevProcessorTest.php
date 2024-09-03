<?php

use EasyIni\Processors\DevProcessor;

it('must make the specified output', function () {
    $input = <<<'EOI'
        [Phar]
        ; https://php.net/phar.readonly
        ;phar.readonly = On

        ; https://php.net/phar.require-hash
        ;phar.require_hash = On
        EOI;

    $expected = <<<'EOI'
        [Phar]
        ; https://php.net/phar.readonly
        phar.readonly = Off

        ; https://php.net/phar.require-hash
        ;phar.require_hash = On
        EOI;

    $options = true;

    $this->performProcessorTest(
        DevProcessor::class,
        $options,
        $input,
        $expected,
    );
});
