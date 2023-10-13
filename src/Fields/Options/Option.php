<?php

namespace Cone\Root\Fields\Options;

use Cone\Root\Traits\HasAttributes;
use Cone\Root\Traits\Makeable;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Traits\Conditionable;

class Option implements Arrayable
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
        $this->setAttribute('value', $value);
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
