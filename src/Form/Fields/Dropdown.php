<?php

namespace Cone\Root\Form\Fields;

use Cone\Root\Form\Fields\Options\DropdownOption;
use Cone\Root\Form\Form;
use Illuminate\Support\Collection;

class Dropdown extends Select
{
    /**
     * The Blade template.
     */
    protected string $template = 'root::form.fields.dropdown';

    /**
     * Create a new field instance.
     */
    public function __construct(Form $form, string $label, string $modelAttribute = null)
    {
        parent::__construct($form, $label, $modelAttribute);

        $this->setAttribute('class', 'form-control combobox__control');
    }

    /**
     * Make a new option instance.
     */
    public function newOption(mixed $value, string $label): DropdownOption
    {
        return new DropdownOption($value, $label);
    }

    /**
     * {@inheritdoc}
     */
    public function toArray(): array
    {
        $data = parent::toArray();

        return array_merge($data, [
            'options' => array_map(static function (DropdownOption $option): array {
                return $option->toRenderedArray();
            }, $data['options']),
            'selection' => Collection::make($data['options'])
                ->filter(function (DropdownOption $option): bool {
                    return $option->getAttribute('selected');
                })
                ->values()
                ->map(function (DropdownOption $option): array {
                    return $option->toRenderedArray();
                })
                ->toArray(),
            'config' => [
                'multiple' => $this->getAttribute('multiple'),
            ],
        ]);
    }
}
