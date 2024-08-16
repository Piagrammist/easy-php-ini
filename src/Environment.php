<?php declare(strict_types=1);

namespace EasyIni;

class Environment
{
    public function __construct(protected bool $dev) {}

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
            default => null,
        };
        if ($dev === null) {
            throw new \InvalidArgumentException('Wrong environment mode');
        }
        $this->dev = $dev;
        return $this;
    }
}
