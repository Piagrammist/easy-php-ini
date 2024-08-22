<?php declare(strict_types=1);

namespace EasyIni;

class Ini extends Environment
{
    public function getIniPath(bool $template): string
    {
        $p = php_ini_loaded_file();
        if ($p && is_file($p)) {
            return $p;
        }
        if (static::IS_WIN) {
            $p = path(PHP_BINDIR, 'php.ini');
            if (is_file($p)) {
                return $p;
            }
            if ($template) {
                $p .= $this->dev ? '-development' : '-production';
                if (is_file($p)) {
                    return $p;
                }
            }
        }
        throw new \RuntimeException("Could not resolve the ini path");
    }

    protected function readIni(): string
    {
        return file_get_contents($this->getIniPath(template: true));
    }
    protected function writeIni(string $content): bool
    {
        $iniPath = $this->getIniPath(template: false);
        Logger::info("Writing to '$iniPath'.");
        return (bool)file_put_contents($iniPath, $content);
    }
}
