<?php

namespace Cone\Root\Fields;

use Closure;
use Cone\Root\Resources\Resource;
use Cone\Root\Support\Collections\Fields;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class BelongsToMany extends BelongsTo
{
    /**
     * The pivot fields resolver.
     *
     * @var \Closure|null
     */
    protected ?Closure $pivotFieldsResolver = null;

    /**
     * The resolved store.
     *
     * @var array
     */
    protected array $resolved = [];

    /**
     * Handle the event when the object is resolved.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Cone\Root\Resources\Resource  $resource
     * @param  string  $key
     * @return void
     */
    public function resolved(Request $request, Resource $resource, string $key): void
    {
        parent::resolved($request, $resource, $key);

        $this->resolvePivotFields($request)->resolved($request, $resource, $key);
    }

    /**
     * Hydrate the model.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @param  mixed  $value
     * @return void
     */
    public function hydrate(Request $request, Model $model, mixed $value): void
    {
        $model->saved(function (Model $model) use ($value): void {
            call_user_func([$model, $this->relation])->sync($value);
        });
    }

    /**
     * Set the pivot fields resolver.
     *
     * @param  array|\Closre  $value
     * @return $this
     */
    public function withPivotFields(array|Closure $value): static
    {
        if (is_array($value)) {
            $value = static function () use ($value): array {
                return $value;
            };
        }

        $this->pivotFieldsResolver = $value;

        return $this;
    }

    /**
     * Resolve the pivot fields.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Cone\Root\Support\Collections\Fields
     */
    public function resolvePivotFields(Request $request): Fields
    {
        if (! isset($this->resolved['pivot_fields'])) {
            $fields = Fields::make();

            if (! is_null($this->pivotFieldsResolver)) {
                $fields = $fields->merge(call_user_func_array($this->pivotFieldsResolver, [$request]));
            }

            $this->resolved['pivot_fields'] = $fields;
        }

        return $this->resolved['pivot_fields'];
    }

    /**
     * Get the input representation of the field.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @return array
     */
    public function toInput(Request $request, Model $model): array
    {
        return array_merge(parent::toInput($request, $model), [
            'fields' => $this->resolvePivotFields($request)->available($request)->toArray(),
            'multiple' => true,
        ]);
    }
}
