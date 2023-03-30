<?php

namespace Cone\Root\Resources;

use Cone\Root\Support\Collections\Fields;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Http\Request;

class Item implements Arrayable
{
    /**
     * The model instance.
     */
    protected Model $model;

    /**
     * Create a new item instance.
     */
    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    /**
     * Get the resource display representation of the model.
     */
    public function toDisplay(Request $request, Fields $fields): array
    {
        return array_merge($this->toArray($request), [
            'abilities' => [],
            'fields' => $fields->mapToDisplay($request, $this->model)->toArray(),
            'url' => null,
        ]);
    }

    /**
     * Get the resource form representation of the model.
     */
    public function toForm(Request $request, Fields $fields): array
    {
        $fields = $fields->mapToForm($request, $this->model)->toArray();

        return array_merge($this->toArray($request), [
            'abilities' => [],
            'data' => array_reduce($fields, static function (array $data, array $field): array {
                return array_replace_recursive($data, [$field['name'] => $field['value']]);
            }, []),
            'fields' => $fields,
            'url' => null,
        ]);
    }

    /**
     * Get the instance as an array.
     *
     * @return array<TKey, TValue>
     */
    public function toArray(): array
    {
        return [
            'exists' => $this->model->exists,
            'id' => $this->model->getKey(),
            'trashed' => in_array(SoftDeletes::class, class_uses_recursive($this->model)) && $this->model->trashed(),
        ];
    }
}
