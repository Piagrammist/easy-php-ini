<?php declare(strict_types=1);

namespace EasyIni;

class Ini extends Environment
{
    public function findIni(bool $template): string
    {
        $p = \php_ini_loaded_file();
        if ($p && \is_file($p)) {
            return $p;
        }
        if (IS_WIN) {
            $p = path(\PHP_BINDIR, 'php.ini');
            if (\is_file($p)) {
                return $p;
            }
            if ($template) {
                $p .= $this->dev ? '-development' : '-production';
                if (\is_file($p)) {
                    return $p;
                }
            }
        }
        Logger::error(Lang::get('err_ini_resolve'), true);
        return ''; // for the sake of dumb IDEs
    }

    protected function readIni(?string $customPath = null): string
    {
        if ($customPath && !\is_file($customPath)) {
            Logger::error(Lang::get('err_file_resolve', $customPath), true);
        }
        $iniPath = $customPath ?: $this->findIni(template: true);
        Logger::debug(Lang::get('ini_read', $iniPath));
        return \file_get_contents($iniPath);
    }

    protected function writeIni(string $content, ?string $customPath = null): bool
    {
        $iniPath = $customPath ?: $this->findIni(template: false);
        Logger::info(Lang::get('ini_write', $iniPath));
        return (bool)\file_put_contents($iniPath, $content);
    }
}
