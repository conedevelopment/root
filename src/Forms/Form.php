<?php

namespace Cone\Root\Forms;

use Closure;
use Cone\Root\Support\Collections\Fields;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

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
     * The URL resolver callback.
     */
    protected ?Closure $urlResolver = null;

    /**
     * Create a new form instance.
     */
    public function __construct(Model $model, Fields $fields)
    {
        $this->model = $model;
        $this->fields = $fields;
    }

    /**
     * Determine if the model is trashed.
     */
    public function isTrashed(): bool
    {
        return in_array(SoftDeletes::class, class_uses_recursive($this->model)) && $this->model->trashed();
    }

    /**
     * Handle the form request.
     */
    public function handle(Request $request): void
    {
        $this->validate($request);

        $this->fields->each->persist($request, $this->model);

        $this->model->save();
    }

    /**
     * Validate the form using the given request.
     */
    public function validate(Request $request): array
    {
        return $request->validate(
            $this->fields->mapToValidate($request, $this->model)
        );
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
            ? Str::start($request->path(), '/')
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

    /**
     * Get the form schema.
     */
    public function toSchema(Request $request): array
    {
        return [
            'exists' => $this->model->exists,
            'id' => $this->model->getKey(),
            'trashed' => $this->isTrashed(),
            'fields' => $fields = $this->fields->mapToForm($request, $this->model)->toArray(),
            'data' => array_reduce($fields, static function (array $data, array $field): array {
                return array_replace_recursive($data, [$field['name'] => $field['value']]);
            }, []),
            'abilities' => $this->resolveAbilities($request),
            'url' => $this->resolveUrl($request),
        ];
    }
}
