<?php

declare(strict_types=1);

namespace Cone\Root\Fields;

class Checkbox extends Select
{
    /**
     * The Blade template.
     */
    protected string $template = 'root::fields.checkbox';

    /**
     * {@inheritdoc}
     */
    public function multiple(bool $value = true): static
    {
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function size(int $value): static
    {
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function newOption(mixed $value, string $label): Option
    {
        return (new CheckboxOption($value, $label))
            ->class('form-check__control')
            ->setAttributes([
                'type' => 'checkbox',
                'name' => sprintf('%s[]', $this->getModelAttribute()),
            ]);
    }
}
