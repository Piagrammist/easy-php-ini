<?php declare(strict_types=1);

namespace Tests;

use Monolog\Level;
use EasyIni\Logger;
use EasyIni\PatternPairs;
use PHPUnit\Framework\TestCase;

class ProcessorTestCase extends TestCase
{
    public function performProcessorTest(
        string $processor,
        mixed $options,
        string $input,
        string $expected,
        int $limit = 1,
    ): void {
        if (!class_exists($processor)) {
            throw new \InvalidArgumentException("No class named '$processor' found");
        }
        if (!method_exists($processor, 'process')) {
            throw new \InvalidArgumentException("No method named '$processor::process' found");
        }
        Logger::setLevel(Level::Emergency);
        $patterns = new PatternPairs;
        $processor::process($input, $patterns, $options);
        $output = preg_replace(
            $patterns->getLookups(),
            $patterns->getReplacements(),
            $input,
            $limit,
        );
        expect(trimCR($output))->toBe(trimCR($expected));
    }
}
