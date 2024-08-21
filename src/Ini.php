<?php declare(strict_types=1);

namespace EasyIni;

class Ini extends Environment
{
    public function getIniPath(): string
    {
        if(strtoupper(substr(PHP_OS,0,3)) === 'WIN' || PHP_OS_FAMILY === 'Windows'){
            $p = path(PHP_BINDIR,'php.ini');
            if (is_file($p)) {
                return $p;
            }
            $p .= $this->dev ? '-development' : '-production';
            if (is_file($p)) {
                return $p;
            }
        }
        $p = php_ini_loaded_file();
        if(is_file($p)){
            return $p;
        }
        throw new \RuntimeException("ini does not exist @ '$p'");
    }

    public function readIni(): string
    {
        return file_get_contents($this->getIniPath());
    }
    protected static function writeIni(string $content): bool
    {
        Logger::info("Writing to '" . $iniPath = (new Ini)->getIniPath() . "'.");
        return (bool)file_put_contents(
            $iniPath,
            $content
        );
    }

    public static function comment(bool $condition = true): string
    {
        return $condition ? ';' : '';
    }
}
