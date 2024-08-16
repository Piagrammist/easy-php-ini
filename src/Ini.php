<?php declare(strict_types=1);

namespace EasyIni;

class Ini extends Environment
{
    public function getIniPath(): string
    {
        $p = path(PHP_BINDIR, 'php.ini');
        if (is_file($p)) {
            return $p;
        }

        $p .= $this->dev ? '-development' : '-production';
        if (is_file($p)) {
            return $p;
        }

        throw new \RuntimeException("ini does not exist @ '$p'");
    }

    protected function readIni(): string
    {
        return file_get_contents($this->getIniPath());
    }
    protected static function writeIni(string $content): bool
    {
        return (bool)file_put_contents(
            path(PHP_BINDIR, 'php.ini'),
            $content
        );
    }

    public static function comment(bool $condition = true): string
    {
        return $condition ? ';' : '';
    }
}
