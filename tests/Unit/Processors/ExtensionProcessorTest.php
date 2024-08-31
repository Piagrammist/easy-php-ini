<?php

use Monolog\Level;
use EasyIni\Logger;
use EasyIni\PatternPairs;
use EasyIni\Processors\ExtensionProcessor;

it('must make the specified output', function () {
    Logger::setLevel(Level::Emergency);

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

    $patterns = new PatternPairs;
    $options = [
        'curl',
        'mbstring',
        'exif',
        'zip',
    ];

    ExtensionProcessor::process($input, $patterns, $options);
    $output = preg_replace(
        $patterns->getLookups(),
        $patterns->getReplacements(),
        $input
    );

    expect(trimCR($output))->toBe(trimCR($expected));
})
    ->onlyOnWindows();
