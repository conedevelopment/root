<?php

namespace Cone\Root\Form\Fields;

use Cone\Root\Traits\HasAttributes;
use Cone\Root\Traits\Makeable;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\App;
use Illuminate\View\ComponentAttributeBag;

class Option implements Renderable
{
    use HasAttributes;
    use Makeable;

    /**
     * The blade template.
     */
    protected string $template = 'root::form.fields.option';

    /**
     * The option label.
     */
    protected string $label;

    /**
     * The option value.
     */
    protected ?string $value = null;

    /**
     * Create a new option instance.
     */
    public function __construct(string $label, ?string $value = null)
    {
        $this->label = $label;
        $this->value = $value;
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
     * Render the field.
     */
    public function render(): View
    {
        return App::make('view')->make($this->template, [
            'attrs' => new ComponentAttributeBag($this->resolveAttributes()),
            'label' => $this->label,
            'value' => $this->value,
        ]);
    }
}
