<?php

namespace Cone\Root\Traits;

use Closure;
use Cone\Root\Fields\Field;
use Cone\Root\Fields\Fields;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

trait ResolvesFields
{
    /**
     * The fields collection.
     */
    protected ?Fields $fields = null;

    /**
     * The fields resolver callback.
     */
    protected ?Closure $fieldsResolver = null;

    /**
     * Define the fields for the object.
     */
    public function fields(Request $request): array
    {
        return [];
    }

    /**
     * Set the fields resolver callback.
     */
    public function withFields(Closure $callback): static
    {
        $this->fieldsResolver = $callback;

        return $this;
    }

    /**
     * Resolve the fields collection.
     */
    public function resolveFields(Request $request): Fields
    {
        if (is_null($this->fields)) {
            $this->fields = new Fields($this->fields($request));

            if (! is_null($this->fieldsResolver)) {
                $this->fields->register(
                    Arr::wrap(call_user_func_array($this->fieldsResolver, [$request]))
                );
            }

            $this->fields->each(function (Field $field) use ($request): void {
                $this->resolveField($request, $field);
            });
        }

        return $this->fields;
    }

    /**
     * Handle the callback for the field resolution.
     */
    protected function resolveField(Request $request, Field $field): void
    {
        //
    }

    /**
     * Find the field with the given API URI.
     */
    public function findField(Request $request, string $apiUri): ?Field
    {
        foreach ($this->resolveFields($request)->all() as $field) {
            if (trim($field->getApiUri(), '/') === trim($apiUri, '/')) {
                return $field;
            }

            if (in_array(ResolvesFields::class, class_uses_recursive($field))
                && ! is_null($subfield = $field->findField($request, $apiUri))) {
                return $subfield;
            }
        }

        return null;
    }
}
