<?php

use Monolog\Level;
use EasyIni\Logger;
use EasyIni\PatternPairs;
use EasyIni\Ini\EntryState;
use EasyIni\Options\TemplateOptions;
use EasyIni\Processors\TemplateProcessor;

it('must make the specified output', function () {
    Logger::setLevel(Level::Emergency);

    $input = <<<'EOI'
        EOI;

    $expected = <<<'EOI'
        EOI;

    $patterns = new PatternPairs;
    $options = (new TemplateOptions)
        ->setXxx();

    TemplateProcessor::process($input, $patterns, $options);
    $output = preg_replace(
        $patterns->getLookups(),
        $patterns->getReplacements(),
        $input
    );

    expect(trimCR($output))->toBe(trimCR($expected));
})
    ->todo();
