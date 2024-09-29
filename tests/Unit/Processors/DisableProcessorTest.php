<?php

use EasyIni\Options\DisableOptions;
use EasyIni\Processors\DisableProcessor;

it('must make the specified output', function () {
    $input = <<<'EOI'
        ; This directive allows you to disable certain functions.
        disable_functions =

        ; This directive allows you to disable certain classes.
        disable_classes = ZipArchive,Phar

        ; Some extra content here
        EOI;

    $expected = <<<'EOI'
        ; This directive allows you to disable certain functions.
        disable_functions = exec,shell_exec

        ; This directive allows you to disable certain classes.
        disable_classes = ZipArchive

        ; Some extra content here
        EOI;

    $options = (new DisableOptions)
        ->setStrict(false)
        ->setFunctions(['exec', 'shell_exec'])
        ->setClasses(['ZipArchive']);

    $this->performProcessorTest(
        DisableProcessor::class,
        $options,
        $input,
        $expected,
    );
});
