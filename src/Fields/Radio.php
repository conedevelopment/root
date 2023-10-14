<?php

namespace Cone\Root\Fields;

class Radio extends Checkbox
{
    /**
     * {@inheritdoc}
     */
    public function newOption(mixed $value, string $label): Option
    {
        $option = parent::newOption($value, $label);

        $option->setAttributes([
            'type' => 'radio',
            'class' => 'form-check__control',
            'name' => $this->getModelAttribute(),
        ]);

        return $option;
    }
}
