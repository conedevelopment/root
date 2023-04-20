<?php

namespace Cone\Root\Traits;

use Closure;
use Cone\Root\Fields\Field;
use Cone\Root\Support\Collections\Fields;
use Illuminate\Http\Request;

trait ResolvesFields
{
    /**
     * The fields resolver callback.
     */
    protected ?Closure $fieldsResolver = null;

    /**
     * The resolved fields.
     */
    protected ?Fields $fields = null;

    /**
     * Define the fields for the object.
     */
    public function fields(Request $request): array
    {
        return [];
    }

    /**
     * Set the fields resolver.
     */
    public function withFields(array|Closure $fields): static
    {
        $this->fieldsResolver = is_array($fields) ? fn (): array => $fields : $fields;

        return $this;
    }

    /**
     * Resolve the fields.
     */
    public function resolveFields(Request $request): Fields
    {
        if (is_null($this->fields)) {
            $this->fields = Fields::make()->register($this->fields($request));

            if (! is_null($this->fieldsResolver)) {
                $this->fields->register(call_user_func_array($this->fieldsResolver, [$request]));
            }

            $this->fields->each(function (Field $field) use ($request): void {
                $this->resolveField($request, $field);
            });
        }

        return $this->fields;
    }

    /**
     * Handle the resolving event on the field instance.
     */
    protected function resolveField(Request $request, Field $field): void
    {
        //
    }
}
