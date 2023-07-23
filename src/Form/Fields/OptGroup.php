<?php

namespace Cone\Root\Form\Fields;

use Cone\Root\Traits\HasAttributes;
use Cone\Root\Traits\Makeable;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\App;
use Illuminate\View\ComponentAttributeBag;

class OptGroup implements Renderable
{
    use HasAttributes;
    use Makeable;

    /**
     * The blade template.
     */
    protected string $template = 'root::form.fields.optgroup';

    /**
     * The options.
     */
    protected array $options = [];

    /**
     * Create a new option group instance.
     */
    public function __construct(string $label, array $options = [])
    {
        $this->setAttributes([
            'label' => $label,
            'disabled' => false,
        ]);

        $this->options = $options;
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
     * Set the "label" HTML attribute.
     */
    public function label(string $value): static
    {
        return $this->setAttribute('label', $value);
    }

    /**
     * Set the "disabled" HTML attribute.
     */
    public function disabled(bool $value = true): static
    {
        return $this->setAttribute('disabled', $value);
    }

    /**
     * Render the option group.
     */
    public function render(): View
    {
        return App::make('view')->make($this->template, [
            'attrs' => new ComponentAttributeBag($this->resolveAttributes()),
            'options' => $this->options,
        ]);
    }
}