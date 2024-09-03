<?php

use EasyIni\Processors\DisablingProcessor;

it('must make the specified output', function () {
    $input = <<<'EOI'
        ; This directive allows you to disable certain functions.
        ; It receives a comma-delimited list of function names.
        disable_functions =

        ; This directive allows you to disable certain classes.
        ; It receives a comma-delimited list of class names.
        disable_classes = ZipArchive,Phar

        ; Some extra content here
        EOI;

    $expected = <<<'EOI'
        ; This directive allows you to disable certain functions.
        ; It receives a comma-delimited list of function names.
        disable_functions = exec,shell_exec

        ; This directive allows you to disable certain classes.
        ; It receives a comma-delimited list of class names.
        disable_classes = ZipArchive

        ; Some extra content here
        EOI;

    $options = [
        'functions' => ['exec', 'shell_exec'],
        'classes'   => ['ZipArchive'],
    ];

    $this->performProcessorTest(
        DisablingProcessor::class,
        $options,
        $input,
        $expected,
    );
});
