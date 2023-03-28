<?php

namespace Cone\Root\Forms;

use Cone\Root\Support\Collections\Fields;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class Form
{
    /**
     * The model instance.
     */
    protected Model $model;

    /**
     * The fields collection.
     */
    protected Fields $fields;

    /**
     * Create a new form instance.
     */
    public function __construct(Model $model, Fields $fields)
    {
        //
    }

    /**
     * Handle the form action on the model.
     */
    public function handle(Request $request): void
    {
        //
    }

    /**
     * Validate the form data.
     */
    public function validate(Request $request): array
    {
        return [];
    }

    /**
     * Build the form.
     */
    public function build(Request $request): array
    {
        return [];
    }
}
