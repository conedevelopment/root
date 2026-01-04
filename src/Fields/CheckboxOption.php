<?php

declare(strict_types=1);

namespace Cone\Root\Fields;

use Closure;

class CheckboxOption extends Option
{
    /**
     * Set the "selected" HTML attribute.
     */
    public function selected(Closure|bool $value = true): static
    {
        return $this->checked($value);
    }

    /**
     * Set the "selected" HTML attribute.
     */
    public function checked(Closure|bool $value = true): static
    {
        $value = $value instanceof Closure ? call_user_func_array($value, [$this]) : $value;

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
