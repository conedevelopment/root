<?php

namespace Cone\Root\Table\Filters;

use Cone\Root\Form\Fields\Field;
use Cone\Root\Form\Form;
use Illuminate\Http\Request;

class FilterForm extends Form
{
    /**
     * The Blade template.
     */
    protected string $template = 'root::table.filters.form';

    /**
     * {@inheritdoc}
     */
    public function handle(Request $request): void
    {
        //
    }

    /**
     * {@inheritdoc}
     */
    public function method(): string
    {
        return 'GET';
    }

    /**
     * {@inheritdoc}
     */
    public function data(Request $request): array
    {
        return array_merge(parent::data($request), [
            'search' => $this->fields->first(fn (Field $field): bool => $field instanceof SearchField),
            'fields' => $this->fields->reject(fn (Field $field): bool => $field instanceof SearchField)->all(),
        ]);
    }
}
