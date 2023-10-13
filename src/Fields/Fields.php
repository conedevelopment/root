<?php

namespace Cone\Root\Fields;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Traits\ForwardsCalls;

class Fields
{
    use ForwardsCalls;

    /**
     * The fields collection.
     */
    protected Collection $fields;

    /**
     * Create a new fields instance.
     */
    public function __construct(array $fields = [])
    {
        $this->fields = new Collection($fields);
    }

    /**
     * Register the given fields.
     */
    public function register(array|Field $fields): static
    {
        foreach (Arr::wrap($fields) as $field) {
            $this->fields->push($field);
        }

        return $this;
    }

    /**
     * Persist the request value on the model.
     */
    public function persist(Request $request, Model $model): void
    {
        $this->fields->each(static function (Field $field) use ($request, $model): void {
            $field->persist(
                $request, $model, $field->getValueForHydrate($request)
            );
        });
    }

    /**
     * Map the fields to validate.
     */
    public function mapToValidate(Request $request, Model $model): array
    {
        return $this->fields->reduce(static function (array $rules, Field $field) use ($request, $model): array {
            return array_merge_recursive($rules, $field->toValidate($request, $model));
        }, []);
    }

    /**
     * Map the field to form components.
     */
    public function mapToFormComponents(Request $request, Model $model): array
    {
        return $this->fields->map->toFormComponent($request, $model)->all();
    }

    /**
     * Handle the dynamic method call.
     */
    public function __call($method, $parameters): mixed
    {
        return $this->forwardCallTo($this->fields, $method, $parameters);
    }
}
