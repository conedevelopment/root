<?php

declare(strict_types=1);

namespace Cone\Root\Fields;

class CheckboxOption extends Option
{
    /**
     * Set the "selected" HTML attribute.
     */
    public function selected(bool $value = true): static
    {
        return $this->checked($value);
    }

    /**
     * Set the "selected" HTML attribute.
     */
    public function checked(bool $value = true): static
    {
        return $this->setAttribute('checked', $value);
    }

    /**
     * Get the array representation of the object.
     */
    public function toArray(): array
    {
        $value = parent::toArray();

        unset($value['selected']);

        return array_merge($value, ['checked' => $this->getAttribute('checked')]);
    }
}
