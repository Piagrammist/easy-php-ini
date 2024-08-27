<?php declare(strict_types=1);

namespace EasyIni;

class Ini extends Environment
{
    public function findIni(bool $template): string
    {
        $p = php_ini_loaded_file();
        if ($p && is_file($p)) {
            return $p;
        }
        if (self::IS_WIN) {
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
        Logger::error('Could not resolve the ini path', true);
        return ''; // for the sake of IDEs
    }

    protected function readIni(?string $customPath = null): string
    {
        if ($customPath && !is_file($customPath)) {
            Logger::error("File does not exist at '$customPath'", true);
        }
        $iniPath = $customPath ?: $this->findIni(template: true);
        Logger::debug("Using '$iniPath' as template.");
        return file_get_contents($iniPath);
    }

    protected function writeIni(string $content, ?string $customPath = null): bool
    {
        if ($customPath && !is_file($customPath)) {
            Logger::error("File does not exist at '$customPath'", true);
        }
        $iniPath = $customPath ?: $this->findIni(template: false);
        Logger::info("Writing to '$iniPath'.");
        return (bool)file_put_contents($iniPath, $content);
    }
}
