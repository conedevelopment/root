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

    /**
     * Get the form instance.
     */
    public function form(Request $request): Form
    {
        if (is_null($this->form)) {
            $this->form = $this->toForm($request);
        }

        return $this->form;
    }
}
