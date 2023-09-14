<?php

namespace Cone\Root\Form\Fields;

use Cone\Root\Support\Element;
use Cone\Root\Traits\Makeable;

class OptGroup extends Element
{
    use Makeable;

    /**
     * The Blade template.
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
     * {@inheritdoc}
     */
    public function toArray(): array
    {
        return array_merge(parent::toArray(), [
            'options' => $this->options,
        ]);
    }
}
