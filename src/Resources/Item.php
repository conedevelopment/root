<?php

namespace Cone\Root\Resources;

use Cone\Root\Support\Collections\Fields;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

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
     * Get the policy.
     */
    public function getPolicy(): mixed
    {
        return Gate::getPolicyFor($this->model);
    }

    /**
     * Map the abilities for the model.
     */
    public function mapAbilities(Request $request): array
    {
        $poilicy = $this->getPolicy();

        $user = $request->user();

        return ! $this->model->exists
            ? [
                'viewAny' => is_null($poilicy) || $user->can('viewAny', get_class($this->model)),
                'create' => is_null($poilicy) || $user->can('create', get_class($this->model)),
            ]
            : [
                'view' => is_null($poilicy) || $user->can('view', $this->model),
                'update' => is_null($poilicy) || $user->can('update', $this->model),
                'delete' => is_null($poilicy) || $user->can('delete', $this->model),
                'restore' => is_null($poilicy) || $user->can('restore', $this->model),
                'forceDelete' => is_null($poilicy) || $user->can('forceDelete', $this->model),
            ];
    }

    /**
     * Get the resource display representation of the model.
     */
    public function toDisplay(Request $request, Fields $fields): array
    {
        return array_merge($this->toArray($request), [
            'abilities' => $this->mapAbilities($request),
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
