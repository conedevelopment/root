<?php

namespace Cone\Root\Support;

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
     * Get the form schema.
     */
    public function toSchema(Request $request): array
    {
        return [
            'fields' => $fields = $this->fields->mapToForm($request, $this->model)->toArray(),
            'data' => array_reduce($fields, static function (array $data, array $field): array {
                return array_replace_recursive($data, [$field['name'] => $field['value']]);
            }, []),
        ];
    }
}
