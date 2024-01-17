<?php

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
    public function newOption(mixed $value, string $label): Option
    {
        return parent::newOption($value, $label)
            ->class('form-check__control')
            ->setAttributes([
                'type' => 'checkbox',
                'name' => sprintf('%s[]', $this->getModelAttribute()),
            ]);
    }
}
