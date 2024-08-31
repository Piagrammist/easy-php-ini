<?php declare(strict_types=1);

namespace EasyIni\Ini;

final class EntryValue implements \JsonSerializable
{
    private mixed $value;
    private EntryState $state;

    public function __construct(
        mixed $value = null,
        EntryState $state = EntryState::UNTOUCHED,
    ) {
        $this->set($value, $state);
    }

    public function set(mixed $value, EntryState $state): self
    {
        return $this
            ->setValue($value)
            ->setState($state);
    }

    public function setValue(mixed $value): self
    {
        $this->value = $value;
        return $this;
    }
    public function setValueIfNull(mixed $value): self
    {
        if ($this->value === null) {
            $this->setValue($value);
        }
        return $this;
    }
    public function getRawValue(): mixed
    {
        return $this->value;
    }
    public function getValue(BooleanFormat $format = BooleanFormat::BINARY): mixed
    {
        if ($this->value === null || $this->state === EntryState::UNTOUCHED) {
            return '\2';
        }
        if (is_bool($this->value)) {
            return $format->get($this->value);
        }
        return $this->value;
    }

    public function setState(EntryState $state): self
    {
        $this->state = $state;
        return $this;
    }
    public function setStateIfUntouched(EntryState $state): self
    {
        if ($this->untouched()) {
            $this->setState($state);
        }
        return $this;
    }
    public function getState(): EntryState
    {
        return $this->state;
    }

    public function untouched(): bool
    {
        return $this->getState() === EntryState::UNTOUCHED;
    }
    public function toComment(): bool
    {
        return $this->getState() === EntryState::COMMENT;
    }
    public function toUncomment(): bool
    {
        return $this->getState() === EntryState::UNCOMMENT;
    }

    public function jsonSerialize(): array
    {
        return [
            'value' => $this->getRawValue(),
            'state' => $this->getState(),
        ];
    }
}
