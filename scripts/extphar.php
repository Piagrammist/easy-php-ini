<?php

$input = __DIR__ . "/easy-ini.phar";
$output = __DIR__ . "/easy-ini";

is_dir($output) && rimraf($output);

$phar = new Phar($input);
$phar->extractTo($output);

function rimraf(string $dir): void
{
    $it = new RecursiveDirectoryIterator($dir, RecursiveDirectoryIterator::SKIP_DOTS);
    $items = new RecursiveIteratorIterator(
        $it,
        RecursiveIteratorIterator::CHILD_FIRST
    );
    foreach ($items as $item) {
        if ($item->isDir()) {
            (__FUNCTION__)($item->getPathname());
        } else {
            unlink($item->getPathname());
        }
    }
    rmdir($dir);
}
