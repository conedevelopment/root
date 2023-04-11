<?php

namespace Cone\Root\Forms;

use Closure;
use Cone\Root\Resources\Resourcable;
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
     * Get the form schema.
     */
    public function toSchema(Request $request): array
    {
        $data = (new Resourcable($this->model))->toForm($request, $this->fields);

        return array_merge($data, [
            'data' => array_reduce($data['fields'], static function (array $data, array $field): array {
                return array_replace_recursive($data, [$field['name'] => $field['value']]);
            }, []),
        ]);
    }
}
