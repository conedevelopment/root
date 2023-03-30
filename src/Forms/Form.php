<?php

namespace Cone\Root\Forms;

use Cone\Root\Resources\Item;
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
        $this->model = $model;
        $this->fields = $fields;
    }

    /**
     * Handle the form action on the model.
     */
    public function handle(Request $request): void
    {
        $this->validate($request);

        $this->fields->each->persist($request, $this->model);

        $this->model->save();
    }

    /**
     * Validate the form data.
     */
    public function validate(Request $request): array
    {
        return $request->validate(
            $this->fields->mapToValidate($request, $this->model)
        );
    }

    /**
     * Build the form.
     */
    public function build(Request $request): array
    {
        return (new Item($this->model))->toForm(
            $request, $this->fields
        );
    }
}
