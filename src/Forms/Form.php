<?php

namespace Cone\Root\Forms;

use Cone\Root\Traits\ResolvesFields;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class Form implements Arrayable
{
    use ResolvesFields;

    /**
     * Create a new form instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Get the instance as an array.
     */
    public function toArray(): array
    {
        return [];
    }

    /**
     * Handle the form.
     */
    public function handle(Request $request, Model $model): void
    {
        $this->validate($request, $model);

        $this->resolveFields($request)->each->persist($request, $model);

        $model->save();
    }

    /**
     * Validate the request.
     */
    public function validate(Request $request, Model $model)
    {
        $rules = $this->resolveFields($request)->mapToValidate($request, $model);

        //
    }

    /**
     * Build the form.
     */
    public function build(Request $request, Model $model): array
    {
        return array_merge($this->toArray(), [
            'id' => $model->getKey(),
            'exists' => $model->exists,
            'fields' => $this->resolveFields($request)->mapToForm($request, $model)->toArray()
        ]);
    }
}
