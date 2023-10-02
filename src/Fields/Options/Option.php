<?php

namespace Cone\Root\Fields\Options;

use Cone\Root\Support\Element;
use Cone\Root\Traits\Makeable;
use Illuminate\Support\Traits\Conditionable;

class Option extends Element
{
    use Conditionable;
    use Makeable;

    /**
     * The Blade template.
     */
    protected string $template = 'root::fields.option';

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
        return array_merge(parent::toArray(), [
            'label' => $this->label,
            'selected' => $this->getAttribute('selected'),
            'value' => $this->getAttribute('value'),
        ]);
    }

    /**
     * Get the array representation of the object that holds the rendered HTML.
     */
    public function toRenderedArray(): array
    {
        $view = $this->render();

        return array_merge($view->getData(), [
            'html' => $view->render(),
        ]);
    }
}
