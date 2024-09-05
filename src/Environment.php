<?php declare(strict_types=1);

namespace EasyIni;

class Environment
{
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
}
