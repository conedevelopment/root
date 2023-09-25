<?php

namespace Cone\Root\Table\Actions;

use Cone\Root\Form\Form;
use Illuminate\Http\Request;

class ActionForm extends Form
{
    /**
     * The Blade template.
     */
    protected string $template = 'root::table.actions.form';

    /**
     * {@inheritdoc}
     */
    public function handle(Request $request): void
    {
        $this->validate($request);
    }
}
