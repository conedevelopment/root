<?php

namespace Cone\Root\Traits;

use Closure;
use Cone\Root\Fields\Field;
use Cone\Root\Http\Requests\RootRequest;
use Cone\Root\Support\Collections\Fields;

trait ResolvesFields
{
    /**
     * The fields resolver callback.
     *
     * @var \Closure|null
     */
    protected ?Closure $fieldsResolver = null;

    /**
     * The resolved fields.
     *
     * @var \Cone\Root\Support\Collections\Fields|null
     */
    protected ?Fields $resolvedFields = null;

    /**
     * Define the fields for the object.
     *
     * @param  \Cone\Root\Http\Requests\RootRequest  $request
     * @return array
     */
    public function fields(RootRequest $request): array
    {
        return [];
    }

    /**
     * Set the fields resolver.
     *
     * @param  array|\Closure  $fields
     * @return $this
     */
    public function withFields(array|Closure $fields): static
    {
        if (is_array($fields)) {
            $fields = static function (RootRequest $request, Fields $collection) use ($fields): Fields {
                return $collection->merge($fields);
            };
        }

        $this->fieldsResolver = $fields;

        return $this;
    }

    /**
     * Resolve the fields.
     *
     * @param  \Cone\Root\Http\Requests\RootRequest  $request
     * @return \Cone\Root\Support\Collections\Fields
     */
    public function resolveFields(RootRequest $request): Fields
    {
        if (is_null($this->resolvedFields)) {
            $fields = Fields::make($this->fields($request));

            if (! is_null($this->fieldsResolver)) {
                $fields = call_user_func_array($this->fieldsResolver, [$request, $fields]);
            }

            $this->resolvedFields = $fields->each(function (Field $field) use ($request): void {
                $this->resolveField($request, $field);
            });
        }

        return $this->resolvedFields;
    }

    /**
     * Handle the resolving event on the field instance.
     *
     * @param  \Cone\Root\Http\Requests\RootRequest  $request
     * @param  \Cone\Root\Fields\Field  $field
     * @return void
     */
    protected function resolveField(RootRequest $request, Field $field): void
    {
        //
    }
}
