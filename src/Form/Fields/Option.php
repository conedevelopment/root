<?php

namespace Cone\Root\Form\Fields;

use Cone\Root\Traits\HasAttributes;
use Cone\Root\Traits\Makeable;
use Illuminate\Contracts\Support\Arrayable;

class Option implements Arrayable
{
    use HasAttributes;
    use Makeable;

    /**
     * Create a new option instance.
     */
    public function __construct(string $label, string $value)
    {
        $this->setAttributes([
            'formattedValue' => $label,
            'value' => $value,
        ]);
    }

    /**
     * Set the label attribute.
     */
    public function label(string $value): static
    {
        return $this->setAttribute('label', $value);
    }

    /**
     * Set the disabled attribute.
     */
    public function disabled(bool $value = true): static
    {
        return $this->setAttribute('disabled', $value);
    }

    /**
     * Get the instance as an array.
     */
    public function toArray(): array
    {
        return $this->getAttributes();
    }
}
