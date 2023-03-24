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

    // handle
    // validate

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
