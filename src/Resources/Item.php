<?php

namespace Cone\Root\Resources;

use Cone\Root\Support\Collections\Fields;
use Cone\Root\Traits\MapsAbilities;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class Item implements Arrayable
{
    use MapsAbilities;

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
     * Get the model instance.
     */
    public function getModel(): Model
    {
        return $this->model;
    }

    /**
     * Get the mappable abilities.
     */
    public function getAbilities(): array
    {
        return ['view', 'update', 'delete', 'restore', 'forceDelete'];
    }

    /**
     * Map the URL for the model.
     */
    protected function mapUrl(Request $request): string
    {
        return $this->model->exists
            ? sprintf('%s/%s', $request->route('rootResource')->getUri(), $this->model->getKey())
            : $request->route('rootResource')->getUri();
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

    /**
     * Get the resource display representation of the model.
     */
    public function toDisplay(Request $request, Fields $fields): array
    {
        return array_merge($this->toArray(), [
            'abilities' => $this->mapAbilities($request, $this->model),
            'fields' => $fields->mapToDisplay($request, $this->model)->toArray(),
            'url' => $this->mapUrl($request),
        ]);
    }

    /**
     * Get the resource form representation of the model.
     */
    public function toForm(Request $request, Fields $fields): array
    {
        $fields = $fields->mapToForm($request, $this->model)->toArray();

        return array_merge($this->toArray(), [
            'abilities' => $this->mapAbilities($request, $this->model),
            'data' => array_reduce($fields, static function (array $data, array $field): array {
                return array_replace_recursive($data, [$field['name'] => $field['value']]);
            }, []),
            'fields' => $fields,
            'url' => $this->mapUrl($request),
        ]);
    }
}
