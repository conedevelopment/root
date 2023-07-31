<?php

namespace Cone\Root\Form\Fields;

use Cone\Root\Traits\HasAttributes;
use Cone\Root\Traits\Makeable;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\App;
use Stringable;

class Option implements Stringable
{
    use HasAttributes;
    use Makeable;

    /**
     * The Blade template.
     */
    protected string $template = 'root::form.fields.option';

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
     * Render the option.
     */
    public function render(): View
    {
        return App::make('view')->make($this->template, [
            'attrs' => $this->newAttributeBag(),
            'label' => $this->label,
        ]);
    }

    /**
     * Convert the option to a string.
     */
    public function __toString(): string
    {
        return $this->render()->render();
    }
}
