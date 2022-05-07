<?php

namespace Cone\Root\Traits;

use Closure;
use Cone\Root\Support\Collections\Fields;
use Illuminate\Http\Request;

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
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function fields(Request $request): array
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
            $fields = static function (Request $request, Fields $collection) use ($fields): Fields {
                return $collection->merge($fields);
            };
        }

        $this->fieldsResolver = $fields;

        return $this;
    }

    /**
     * Resolve the fields.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Cone\Root\Support\Collections\Fields
     */
    public function resolveFields(Request $request): Fields
    {
        if (is_null($this->resolvedFields)) {
            $fields = Fields::make($this->fields($request));

            if (! is_null($this->fieldsResolver)) {
                $fields = call_user_func_array($this->fieldsResolver, [$request, $fields]);
            }

            $this->resolvedFields = $fields->each->mergeAuthorizationResolver(function (...$parameters): bool {
                return $this->authorized(...$parameters);
            });
        }

        return $this->resolvedFields;
    }
}
