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
     * @param  string  $label
     * @param  string  $value
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
     * Get the instance as an array.
     *
     * @return array<TKey, TValue>
     */
    public function toArray(): array
    {
        return $this->getAttributes();
    }
}
