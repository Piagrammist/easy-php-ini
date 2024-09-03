<?php

uses(Tests\ProcessorTestCase::class)
    ->group('processors')
    ->in('Unit/Processors/*ProcessorTest.php');

function trimCR(string $text): string
{
    return str_replace("\r", '', $text);
}
