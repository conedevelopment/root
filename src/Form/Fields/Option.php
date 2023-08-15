<?php

namespace Cone\Root\Form\Fields;

use Cone\Root\Traits\HasAttributes;
use Cone\Root\Traits\Makeable;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\App;
use JsonSerializable;
use Stringable;

class Option implements Arrayable, Stringable, JsonSerializable
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
     * Get the JSON serializable format of the object.
     */
    public function jsonSerialize(): array
    {
        return $this->toArray();
    }

    /**
     * Convert the option to a string.
     */
    public function __toString(): string
    {
        return $this->render()->render();
    }

    /**
     * Get the array representation of the object.
     */
    public function toArray(): array
    {
        return [
            'attrs' => $this->resolveAttributes(),
            'label' => $this->label,
            'value' => $this->getAttribute('value'),
        ];
    }
}
