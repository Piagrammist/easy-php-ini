<?php

use EasyIni\Processors\ExtensionProcessor;

it('must make the specified output', function () {
    $input = <<<'EOI'
        ; Directory in which the loadable extensions (modules) reside.
        ;extension_dir = "./"
        ; On windows:
        ;extension_dir = "ext"

        ;extension=bz2
        ;extension=curl
        ;extension=ldap
        ;extension=mbstring
        ;extension=exif      ; Must be after mbstring as it depends on it
        ;extension=mysqli
        ;extension=sqlite3
        ;extension=zip

        ;zend_extension=opcache
        EOI;

    $expected = <<<'EOI'
        ; Directory in which the loadable extensions (modules) reside.
        ;extension_dir = "./"
        ; On windows:
        extension_dir = "ext"

        ;extension=bz2
        extension=curl
        ;extension=ldap
        extension=mbstring
        extension=exif      ; Must be after mbstring as it depends on it
        ;extension=mysqli
        ;extension=sqlite3
        extension=zip

        ;zend_extension=opcache
        EOI;

    $options = [
        'curl',
        'mbstring',
        'exif',
        'zip',
    ];

    $this->performProcessorTest(
        ExtensionProcessor::class,
        $options,
        $input,
        $expected,
        -1,
    );
})
    ->onlyOnWindows();
