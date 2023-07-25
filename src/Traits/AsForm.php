<?php

namespace Cone\Root\Traits;

use Cone\Root\Form\Form;
use Illuminate\Http\Request;

trait AsForm
{
    /**
     * The form instance.
     */
    protected ?Form $form = null;

    /**
     * Get the form instance for the resource.
     */
    abstract public function toForm(Request $request): Form;
}
