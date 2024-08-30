<?php

function trimCR(string $text): string
{
    return str_replace("\r", '', $text);
}

function data(string $name, bool $returnPath = false): string
{
    $path = __DIR__ . '/data/' . $name;
    if (!is_file($path)) {
        throw new InvalidArgumentException("Could not resolve data file '$path'");
    }
    return $returnPath ? realpath($path) : file_get_contents($path);
}
