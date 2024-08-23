<?php declare(strict_types=1);

namespace EasyIni;

class Environment
{
    public const IS_WIN = PHP_OS_FAMILY === 'Windows';
    protected bool $dev = true;

    public function development(bool $dev = true): static
    {
        $this->dev = $dev;
        return $this;
    }
    public function production(bool $prod = true): static
    {
        $this->dev = !$prod;
        return $this;
    }
    public function env(string $key): static
    {
        $dev = match (strtolower($key)) {
            'p', 'prod', 'production' => false,
            'd', 'dev', 'development' => true,
            default                   => null,
        };
        if ($dev === null) {
            Logger::error("Invalid environment mode '$key'");
        }
        return $this->development($dev);
    }
}
