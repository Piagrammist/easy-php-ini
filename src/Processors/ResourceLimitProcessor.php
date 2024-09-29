<?php declare(strict_types=1);

namespace EasyIni\Processors;

use EasyIni\Options\ResourceLimitOptions;

final class ResourceLimitProcessor extends AbstractProcessor
{
    protected static string $name = 'Resource Limit';
    protected static string $optionsClass = ResourceLimitOptions::class;
}
