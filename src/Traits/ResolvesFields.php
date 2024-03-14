<?php

namespace Cone\Root\Traits;

use Closure;
use Cone\Root\Fields\Field;
use Cone\Root\Fields\Fields;
use Cone\Root\Fields\File;
use Cone\Root\Fields\Media;
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
     * Determine if the object has file field.
     */
    public function hasFileField(Request $request): bool
    {
        return $this->resolveFields($request)
            ->subResource(false)
            ->visible(['update', 'create'])
            ->some(static function (Field $field) use ($request): bool {
                return $field instanceof File && ! $field instanceof Media
                    || (in_array(ResolvesFields::class, class_uses_recursive($field)) && $field->hasFileField($request));
            });
    }

    /**
     * Resolve the fields collection.
     */
    public function resolveFields(Request $request): Fields
    {
        if (is_null($this->fields)) {
            $this->fields = new Fields($this->fields($request));

            $this->fields->when(! is_null($this->fieldsResolver), function (Fields $fields) use ($request): void {
                $fields->register(
                    Arr::wrap(call_user_func_array($this->fieldsResolver, [$request]))
                );
            });

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
}
