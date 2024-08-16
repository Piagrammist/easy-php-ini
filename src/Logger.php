<?php declare(strict_types=1);

namespace EasyIni;

use Monolog\Level;
use Monolog\Logger as MonologLogger;
use Monolog\Handler\StreamHandler;
use Monolog\Formatter\LineFormatter;

final class Logger
{
    private static array $names;
    private static Level $minLevel = Level::Info;
    private static ?MonologLogger $instance = null;

    public static function __callStatic($method, $args): void
    {
        self::init();
        if (in_array($method, self::$names, true)) {
            if (count($args) !== 1) {
                throw new \InvalidArgumentException('The message is required');
            }
            if (!is_string($args[0])) {
                throw new \InvalidArgumentException('Message must be a string');
            }
            self::log($args[0], Level::{ucfirst($method)});
            return;
        }
        throw new \BadMethodCallException(
            'Method ' .
            ((new \ReflectionClass(self::class))->getShortName()) .
            "::$method does not exist"
        );
    }

    public static function setLevel(Level $level): void
    {
        self::$minLevel = $level;
    }

    public static function log(string $message, Level $level): void
    {
        if ($level->value < self::$minLevel->value)
            return;

        self::init();
        self::$instance->log($level, $message);
    }

    private static function init(): void
    {
        if (self::$instance !== null)
            return;

        self::$names = array_map('strtolower', Level::NAMES);

        $formatter = new LineFormatter("[%level_name%] %message%\n");
        $stream = new StreamHandler("php://stdout", Level::Debug);
        $stream->setFormatter($formatter);
        self::$instance = new MonologLogger('default');
        self::$instance->pushHandler($stream);
    }
}
