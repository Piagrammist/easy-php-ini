<?php declare(strict_types=1);

namespace EasyIni\Processors;

use EasyIni\Options\DisableOptions;

final class DisableProcessor extends AbstractProcessor
{
    protected static string $name = 'Disable';
    protected static string $optionsClass = DisableOptions::class;
}
