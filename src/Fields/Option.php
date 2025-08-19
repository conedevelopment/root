<?php

declare(strict_types=1);

namespace Cone\Root\Fields;

use Cone\Root\Traits\HasAttributes;
use Cone\Root\Traits\Makeable;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Traits\Conditionable;
use JsonSerializable;

use function Illuminate\Support\enum_value;

class Option implements Arrayable, JsonSerializable
{
    use Conditionable;
    use HasAttributes;
    use Makeable;

    /**
     * The option label.
     */
    protected string $label;

    /**
     * Create a new option instance.
     */
    public function __construct(mixed $value, string $label)
    {
        $this->label = $label;
        $this->setAttribute('value', enum_value($value));
        $this->selected(false);
    }

    /**
     * Set the "disabled" HTML attribute.
     */
    public function disabled(bool $value = true): static
    {
        return $this->setAttribute('disabled', $value);
    }

    /**
     * Set the "selected" HTML attribute.
     */
    public function selected(bool $value = true): static
    {
        return $this->setAttribute('selected', $value);
    }

    /**
     * Convert the element to a JSON serializable format.
     */
    public function jsonSerialize(): array
    {
        return $this->toArray();
    }

    /**
     * Get the array representation of the object.
     */
    public function toArray(): array
    {
        return [
            'attrs' => $this->newAttributeBag(),
            'label' => $this->label,
            'selected' => $this->getAttribute('selected'),
            'value' => $this->getAttribute('value'),
        ];
    }
}
