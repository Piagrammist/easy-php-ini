<?php declare(strict_types=1);

namespace EasyIni;

trait Strict
{
    private bool $strict = true;

    public function setStrict(bool $strict = true): static
    {
        $this->strict = $strict;
        return $this;
    }

    public function isStrict(): bool
    {
        return $this->strict;
    }
}
