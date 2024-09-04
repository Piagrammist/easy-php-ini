<?php

$input = __DIR__ . "/easy-ini.phar";
$output = __DIR__ . "/easy-ini";

is_dir($output) && rimraf($output);

$phar = new Phar($input);
$phar->extractTo($output);

function rimraf(string $dir): void
{
    foreach (array_diff(scandir($dir), ['.', '..']) as $entry) {
        $path = $dir . DIRECTORY_SEPARATOR . $entry;
        is_dir($path)
            ? (__FUNCTION__)($path)
            : unlink($path);
    }
    rmdir($dir);
}
