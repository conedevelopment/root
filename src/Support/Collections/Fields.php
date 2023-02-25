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
     */
    public function searchable(RootRequest $request): static
    {
        return $this->filter->isSearchable($request);
    }

    /**
     * Filter the sortable fields.
     */
    public function sortable(RootRequest $request): static
    {
        return $this->filter->isSortable($request);
    }

    /**
     * Map the fields to display.
     */
    public function mapToDisplay(RootRequest $request, Model $model): Collection
    {
        return $this->map->toDisplay($request, $model)->toBase();
    }

    /**
     * Map the fields to form.
     */
    public function mapToForm(RootRequest $request, Model $model): Collection
    {
        return $this->map->toInput($request, $model)->toBase();
    }

    /**
     * Map the fields to validate.
     */
    public function mapToValidate(RootRequest $request, Model $model): array
    {
        return $this->reduce(static function (array $rules, Field $field) use ($request, $model): array {
            return array_merge_recursive($rules, $field->toValidate($request, $model));
        }, []);
    }

    /**
     * Register the field routes.
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
