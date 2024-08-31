<?php

use Monolog\Level;
use EasyIni\Logger;
use EasyIni\PatternPairs;
use EasyIni\Ini\EntryState;
use EasyIni\Options\ResourceLimitOptions;
use EasyIni\Processors\ResourceLimitProcessor;

it('must make the specified output', function () {
    Logger::setLevel(Level::Emergency);

    $input = <<<'EOI'
        ;;;;;;;;;;;;;;;;;;;
        ; Resource Limits ;
        ;;;;;;;;;;;;;;;;;;;

        ; Maximum execution time of each script, in seconds
        max_execution_time = 30

        ; Maximum amount of time each script may spend parsing request data. It's a good
        ; idea to limit this time on productions servers in order to eliminate unexpectedly
        ; long running scripts.
        max_input_time = 60

        ; Maximum input variable nesting level
        ;max_input_nesting_level = 64

        ; How many GET/POST/COOKIE input variables may be accepted
        ;max_input_vars = 1000

        ; How many multipart body parts (combined input variable and file uploads) may
        ; be accepted.
        ;max_multipart_body_parts = 1500

        ; Maximum amount of memory a script may consume
        memory_limit = 128M

        ;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;
        ; Error handling and logging ;
        ;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;
        EOI;

    $expected = <<<'EOI'
        ;;;;;;;;;;;;;;;;;;;
        ; Resource Limits ;
        ;;;;;;;;;;;;;;;;;;;

        ; Maximum execution time of each script, in seconds
        ;max_execution_time = 30

        ; Maximum amount of time each script may spend parsing request data. It's a good
        ; idea to limit this time on productions servers in order to eliminate unexpectedly
        ; long running scripts.
        max_input_time = 30

        ; Maximum input variable nesting level
        ;max_input_nesting_level = 64

        ; How many GET/POST/COOKIE input variables may be accepted
        max_input_vars = 1000

        ; How many multipart body parts (combined input variable and file uploads) may
        ; be accepted.
        ;max_multipart_body_parts = 1500

        ; Maximum amount of memory a script may consume
        memory_limit = 256M

        ;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;
        ; Error handling and logging ;
        ;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;
        EOI;

    $patterns = new PatternPairs;
    $options = (new ResourceLimitOptions)
        ->setMaxExecutionTime(state: EntryState::COMMENT)
        ->setMaxInputTime(30)
        ->setMaxInputVars(state: EntryState::UNCOMMENT)
        ->setMemoryLimit('256M');


    ResourceLimitProcessor::process($input, $patterns, $options);
    $output = preg_replace(
        $patterns->getLookups(),
        $patterns->getReplacements(),
        $input
    );

    expect(trimCR($output))->toBe(trimCR($expected));
});
