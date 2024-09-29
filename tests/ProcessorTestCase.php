<?php declare(strict_types=1);

namespace Tests;

use Monolog\Level;
use EasyIni\Logger;
use PHPUnit\Framework\TestCase;

class ProcessorTestCase extends TestCase
{
    public function performProcessorTest(
        string $class,
        mixed $options,
        string $input,
        string $expected,
    ): void {
        if (!\class_exists($class)) {
            throw new \InvalidArgumentException("No class named '$class' found");
        }
        Logger::setLevel(Level::Emergency);
        $processor = new $class($input);
        $processor->apply($options);
        $output = $processor->replace();
        expect(trimCR($output))->toBe(trimCR($expected));
    }
}
