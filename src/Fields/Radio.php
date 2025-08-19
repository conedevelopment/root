<?php

declare(strict_types=1);

namespace Cone\Root\Fields;

class Radio extends Checkbox
{
    /**
     * {@inheritdoc}
     */
    public function newOption(mixed $value, string $label): Option
    {
        return parent::newOption($value, $label)
            ->class('form-check__control')
            ->setAttributes([
                'type' => 'radio',
                'name' => $this->getModelAttribute(),
            ]);
    }
}
