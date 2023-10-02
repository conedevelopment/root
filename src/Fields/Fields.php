<?php

namespace Cone\Root\Fields;

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
    public function persist(Request $request): void
    {
        $this->fields->each(static function (Field $field) use ($request): void {
            $field->persist(
                $request, $field->getValueForHydrate($request)
            );
        });
    }

    /**
     * Map the fields to validate.
     */
    public function mapToValidate(Request $request): array
    {
        return $this->fields->reduce(static function (array $rules, Field $field) use ($request): array {
            return array_merge_recursive($rules, $field->toValidate($request));
        }, []);
    }

    /**
     * Handle the dynamic method call.
     */
    public function __call($method, $parameters): mixed
    {
        return $this->forwardCallTo($this->fields, $method, $parameters);
    }
}
