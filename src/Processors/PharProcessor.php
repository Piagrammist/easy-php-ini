<?php declare(strict_types=1);

namespace EasyIni\Processors;

use EasyIni\Options\PharOptions;

final class PharProcessor extends AbstractProcessor
{
    protected static string $name = 'Phar';
    protected static string $optionsClass = PharOptions::class;
}
