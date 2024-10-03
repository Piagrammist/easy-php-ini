<?php

use EasyIni\Ini\EntryState;
use EasyIni\Ini\ValueFormat;
use EasyIni\Options\PharOptions;
use EasyIni\Processors\PharProcessor;

use const EasyIni\IS_WIN;

it('must make the specified output', function () {
    $paths = IS_WIN
        ? ['C:\example.phar', 'D:\path\to\vendor.phar']
        : ['/root/example.phar', '/path/to/vendor.phar'];

    $input = <<<'EOI'
        [Phar]
        ; https://php.net/phar.readonly
        phar.readonly = Off

        ; https://php.net/phar.require-hash
        ;phar.require_hash = On

        ;phar.cache_list =

        ; Some extra content here
        EOI;

    $pathsString = ValueFormat::ARR_PATH->get($paths);
    $expected = <<<EOI
        [Phar]
        ; https://php.net/phar.readonly
        ;phar.readonly = On

        ; https://php.net/phar.require-hash
        phar.require_hash = Off

        phar.cache_list = $pathsString

        ; Some extra content here
        EOI;

    $options = (new PharOptions)
        ->setStrict(false)
        ->setReadonly(true, EntryState::COMMENT)
        ->setRequireHash(false)
        ->setCacheList($paths);

    $this->performProcessorTest(
        PharProcessor::class,
        $options,
        $input,
        $expected,
    );
});
