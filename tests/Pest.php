<?php

pest()
    ->group('arch')
    ->in('Architecture');

pest()
    ->group('functions')
    ->in('Unit/Functions');

pest()
    ->extend(Tests\ProcessorTestCase::class)
    ->group('processors')
    ->in('Unit/Processors');

function trimCR(string $text): string
{
    return str_replace("\r", '', $text);
}
