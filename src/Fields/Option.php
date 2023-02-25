<?php

namespace Cone\Root\Fields;

use Cone\Root\Traits\HasAttributes;
use Cone\Root\Traits\Makeable;
use Illuminate\Contracts\Support\Arrayable;

class Option implements Arrayable
{
    use HasAttributes;
    use Makeable;

    /**
     * Create a new option instance.
     *
     * @return void
     */
    public function __construct(string $label, string $value)
    {
        $this->setAttributes([
            'formatted_value' => $label,
            'value' => $value,
        ]);
    }

    /**
     * Set the disabled attribute.
     *
     * @return $this
     */
    public function disabled(bool $value = true): static
    {
        return $this->setAttribute('disabled', $value);
    }

    /**
     * Get the instance as an array.
     *
     * @return array<TKey, TValue>
     */
    public function toArray(): array
    {
        return $this->getAttributes();
    }
}
