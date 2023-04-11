<?php

namespace Cone\Root\Tables;

use Closure;
use Cone\Root\Support\Collections\Fields;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Http\Request;

class Row
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
     * The URL resolver callback.
     */
    protected ?Closure $urlResolver = null;

    /**
     * Create a new row instance.
     */
    public function __construct(Model $model, Fields $fields)
    {
        $this->model = $model;
        $this->fields = $fields;
    }

    /**
     * Build the table row.
     */
    public function build(Request $request): array
    {
        return [
            'exists' => $this->model->exists,
            'id' => $this->model->getKey(),
            'trashed' => $this->isTrashed(),
            'fields' => $this->fields->mapToDisplay($request, $this->model)->toArray(),
            'abilities' => $this->resolveAbilities($request),
            'url' => $this->resolveUrl($request),
        ];
    }

    /**
     * Determine if the model is trashed.
     */
    public function isTrashed(): bool
    {
        return in_array(SoftDeletes::class, class_uses_recursive($this->model)) && $this->model->trashed();
    }

    /**
     * Set the URL resolver callback.
     */
    public function url(Closure $callback): static
    {
        $this->urlResolver = $callback;

        return $this;
    }

    /**
     * Resolve the URL for the row.
     */
    protected function resolveUrl(Request $request): string
    {
        return is_null($this->urlResolver)
            ? sprintf('%s/%s', $request->url(), $this->model->getRouteKey())
            : call_user_func_array($this->urlResolver, [$request, $this->model]);
    }

    /**
     * Resolve the abilities.
     */
    protected function resolveAbilities(Request $request): array
    {
        return [
            'view' => $request->user()->can('view', $this->model),
            'update' => $request->user()->can('update', $this->model),
            'delete' => $request->user()->can('delete', $this->model),
            'restore' => $request->user()->can('restore', $this->model),
            'forceDelete' => $request->user()->can('forceDelete', $this->model),
        ];
    }
}
