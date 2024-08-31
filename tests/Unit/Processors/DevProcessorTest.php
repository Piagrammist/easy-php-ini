<?php

use Monolog\Level;
use EasyIni\Logger;
use EasyIni\PatternPairs;
use EasyIni\Processors\DevProcessor;

it('must make the specified output', function () {
    Logger::setLevel(Level::Emergency);

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

    $patterns = new PatternPairs;

    DevProcessor::process($input, $patterns, true);
    $output = preg_replace(
        $patterns->getLookups(),
        $patterns->getReplacements(),
        $input
    );

    expect(trimCR($output))->toBe(trimCR($expected));
});
