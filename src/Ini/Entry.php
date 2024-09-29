<?php declare(strict_types=1);

namespace EasyIni\Ini;

use Attribute;
use JsonSerializable;

use EasyIni\Lang;
use function EasyIni\validateSnake;

#[Attribute(Attribute::TARGET_PROPERTY)]
final class Entry implements JsonSerializable
{
    private ?string $name;
    private EntryState $state = EntryState::UNTOUCHED;

    private mixed $value = null;
    private mixed $prevValue = null;
    private ValueFormat $format = ValueFormat::NONE;
    private ValueFormat $prevFormat = ValueFormat::NONE;

    public function __construct(?string $name = null)
    {
        $this->setName($name);
    }

    public function set(mixed $value, EntryState $state, ValueFormat $format = ValueFormat::NONE): self
    {
        return $this
            ->setValue($value, $format)
            ->setState($state);
    }

    public function setName(?string $name = null): self
    {
        if ($name === '') {
            throw new \InvalidArgumentException(Lang::get('err_entry_empty'));
        }
        if ($name !== null && !validateSnake($name)) {
            throw new \InvalidArgumentException(Lang::get('err_entry_snake'));
        }
        $this->name = $name;
        return $this;
    }
    public function getName(): ?string
    {
        return $this->name;
    }

    public function setValue(mixed $value, ValueFormat $format = ValueFormat::NONE): self
    {
        $this->value = $value;
        $this->format = $format;
        return $this;
    }
    public function setValueIfNull(mixed $value, ValueFormat $format = ValueFormat::NONE): self
    {
        if ($this->value === null) {
            $this->setValue($value, $format);
        }
        return $this;
    }
    public function getRawValue(): mixed
    {
        return $this->value;
    }
    public function getValue(): string
    {
        if ($this->value === null || $this->state === EntryState::UNTOUCHED) {
            return '\2';
        }
        if (is_int($this->value)) {
            return (string)$this->value;
        }
        return $this->format->get($this->value);
    }

    public function setPrevValue(mixed $value, ValueFormat $format = ValueFormat::NONE): self
    {
        $this->prevValue = $value;
        $this->prevFormat = $format;
        return $this;
    }
    public function getRawPrevValue(): mixed
    {
        return $this->prevValue;
    }
    public function getPrevValue(): string
    {
        if ($this->prevValue === null) {
            return '.*';
        }
        if (is_int($this->value)) {
            return (string)$this->value;
        }
        return $this->prevFormat->get($this->prevValue);
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
            'name'       => $this->getName(),
            'value'      => $this->getValue(),
            'prev_value' => $this->getPrevValue(),
            'state'      => $this->getState(),
        ];
    }
}
