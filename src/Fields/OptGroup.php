<?php

namespace Cone\Root\Fields;

use Cone\Root\Traits\HasAttributes;
use Cone\Root\Traits\Makeable;
use Illuminate\Contracts\Support\Arrayable;

class OptGroup implements Arrayable
{
    use HasAttributes;
    use Makeable;

    /**
     * The options.
     */
    protected array $options = [];

    /**
     * Create a new option group instance.
     */
    public function __construct(string $label)
    {
        $this->setAttributes([
            'label' => $label,
            'disabled' => false,
        ]);
    }

    /**
     * Set the options attribute.
     */
    public function options(array $value): static
    {
        $this->options = $value;

        return $this;
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
        return array_merge($this->getAttributes(), [
            'options' => $this->options,
        ]);
    }
}
