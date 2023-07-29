<?php

namespace Cone\Root\Table\Actions;

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
}
