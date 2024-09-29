<?php declare(strict_types=1);

namespace EasyIni\Processors;

use EasyIni\Options\ExtensionOptions;

final class ExtensionProcessor extends AbstractProcessor
{
    protected static int $limit = -1;
    protected static string $name = 'Extension';
    protected static string $optionsClass = ExtensionOptions::class;
}
