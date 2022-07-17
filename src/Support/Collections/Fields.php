<?php

namespace Cone\Root\Support\Collections;

use Cone\Root\Fields\Field;
use Cone\Root\Http\Requests\RootRequest;
use Cone\Root\Traits\RegistersRoutes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Routing\Router;
use Illuminate\Support\Collection;

class Fields extends Collection
{
    /**
     * Filter the fields that are available for the given request.
     *
     * @param  \Cone\Root\Http\Requests\RootRequest  $request
     * @param  array ...$parameters
     * @return static
     */
    public function available(RootRequest $request, ...$parameters): static
    {
        return $this->filter(static function (Field $field) use ($request, $parameters): bool {
            return $field->authorized($request, ...$parameters)
                && $field->visible($request);
        })->values();
    }

    /**
     * Filter the searchable fields.
     *
     * @param  \Cone\Root\Http\Requests\RootRequest  $request
     * @return static
     */
    public function searchable(RootRequest $request): static
    {
        return $this->filter->isSearchable($request);
    }

    /**
     * Filter the sortable fields.
     *
     * @param  \Cone\Root\Http\Requests\RootRequest  $request
     * @return static
     */
    public function sortable(RootRequest $request): static
    {
        return $this->filter->isSortable($request);
    }

    /**
     * Map the fields to display.
     *
     * @param  \Cone\Root\Http\Requests\RootRequest  $request
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @return \Illuminate\Support\Collection
     */
    public function mapToDisplay(RootRequest $request, Model $model): Collection
    {
        return $this->map->toDisplay($request, $model)->toBase();
    }

    /**
     * Map the fields to form.
     *
     * @param  \Cone\Root\Http\Requests\RootRequest  $request
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @return \Illuminate\Support\Collection
     */
    public function mapToForm(RootRequest $request, Model $model): Collection
    {
        return $this->map->toInput($request, $model)->toBase();
    }

    /**
     * Map the fields to form data.
     *
     * @param  \Cone\Root\Http\Requests\RootRequest  $request
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @return array
     */
    public function mapToFormData(RootRequest $request, Model $model): array
    {
        return $this->reduce(static function (array $rules, Field $field) use ($request, $model): array {
            return array_merge($rules, $field->toFormData($request, $model));
        }, []);
    }

    /**
     * Map the fields to validate.
     *
     * @param  \Cone\Root\Http\Requests\RootRequest  $request
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @return array
     */
    public function mapToValidate(RootRequest $request, Model $model): array
    {
        return $this->reduce(static function (array $rules, Field $field) use ($request, $model): array {
            return array_merge_recursive($rules, $field->toValidate($request, $model));
        }, []);
    }

    /**
     * Register the field routes.
     *
     * @param  \Cone\Root\Http\Requests\RootRequest  $request
     * @param  \Illuminate\Routing\Router  $router
     * @return void
     */
    public function registerRoutes(RootRequest $request, Router $router): void
    {
        $router->prefix('fields')->group(function (Router $router) use ($request): void {
            $this->each(static function (Field $field) use ($request, $router): void {
                if (in_array(RegistersRoutes::class, class_uses_recursive($field))) {
                    $field->registerRoutes($request, $router);
                }
            });
        });
    }
}
