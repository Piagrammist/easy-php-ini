<?php declare(strict_types=1);

namespace EasyIni\Processors;

use EasyIni\Options\ErrorHandlingOptions;

final class ErrorHandlingProcessor extends AbstractProcessor
{
    protected static string $name = 'Error Handling';
    protected static string $optionsClass = ErrorHandlingOptions::class;
}
