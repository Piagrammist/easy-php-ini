<?php

use EasyIni\Ini\EntryState;
use EasyIni\Options\ExampleOptions;
use EasyIni\Processors\ExampleProcessor;

it('must make the specified output', function () {
    $input = <<<'EOI'
        EOI;

    $expected = <<<'EOI'
        EOI;

    $options = (new ExampleOptions)
        ->setXxx();

    $this->performProcessorTest(
        ExampleProcessor::class,
        $options,
        $input,
        $expected,
    );
})
    ->todo();
