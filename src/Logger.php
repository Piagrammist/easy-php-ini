<?php declare(strict_types=1);

namespace EasyIni;

use Monolog\Level;
use Monolog\Logger as MonologLogger;
use Monolog\Handler\StreamHandler;
use Monolog\Formatter\LineFormatter;

final class Logger
{
    private static Level $minLevel = Level::Info;
    private static ?MonologLogger $instance = null;

    public static function setLevel(Level $level): void
    {
        self::$minLevel = $level;
    }

    public static function error(string $message, bool $exit = false): void
    {
        self::log($message, Level::Error);
        if ($exit) {
            exit(1);
        }
    }

    public static function warning(string $message): void
    {
        self::log($message, Level::Warning);
    }

    public static function notice(string $message): void
    {
        self::log($message, Level::Notice);
    }

    public static function info(string $message): void
    {
        self::log($message, Level::Info);
    }

    public static function debug(string $message): void
    {
        self::log($message, Level::Debug);
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

        $formatter = new LineFormatter("[%level_name%]\t%message%\n");
        $stream = new StreamHandler("php://stdout", Level::Debug);
        $stream->setFormatter($formatter);
        self::$instance = new MonologLogger('default');
        self::$instance->pushHandler($stream);
    }
}
