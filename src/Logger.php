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

    public static function getInstance(): MonologLogger
    {
        return self::$instance ??= self::new();
    }

    private static function new(): MonologLogger
    {
        $formatter = new LineFormatter("[%level_name%]\t%message%" . PHP_EOL);
        $stream = new StreamHandler("php://stdout", Level::Debug);
        $stream->setFormatter($formatter);
        $logger = new MonologLogger('default');
        $logger->pushHandler($stream);
        return $logger;
    }

    public static function setLevel(Level $level): void
    {
        self::$minLevel = $level;
    }

    public static function log(string $message, Level $level, bool $exit = false): void
    {
        if ($level->value < self::$minLevel->value) {
            $exit && exit(1);
            return;
        }
        self::getInstance()->log($level, $message);
        $exit && exit(1);
    }

    public static function error(string $message, bool $exit = false): void
    {
        self::log($message, Level::Error, $exit);
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
}
