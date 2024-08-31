<?php

uses()
    ->group('processors')
    ->in('Unit/Processors/*Processor*.php');

function trimCR(string $text): string
{
    return str_replace("\r", '', $text);
}
