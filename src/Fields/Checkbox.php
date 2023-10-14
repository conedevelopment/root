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
        $option = parent::newOption($value, $label);

        $option->setAttributes([
            'type' => 'checkbox',
            'class' => 'form-check__control',
            'name' => sprintf('%s[]', $this->getModelAttribute()),
        ]);

        return $option;
    }
}
