<?php

namespace Cone\Root\Support\Collections;

use Cone\Root\Fields\Field;
use Cone\Root\Interfaces\Routable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Routing\Router;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

class Fields extends Collection
{
    /**
     * Register the given fields.
     */
    public function register(array|Field $fields): static
    {
        foreach (Arr::wrap($fields) as $field) {
            $this->push($field);
        }

        return $this;
    }

    /**
     * Filter the searchable fields.
     */
    public function searchable(Request $request): static
    {
        return $this->filter->isSearchable($request);
    }

    /**
     * Filter the sortable fields.
     */
    public function sortable(Request $request): static
    {
        return $this->filter->isSortable($request);
    }

    /**
     * Map the fields to display.
     */
    public function mapToDisplay(Request $request, Model $model): Collection
    {
        return $this->map->toDisplay($request, $model)->toBase();
    }

    /**
     * Map the fields to form.
     */
    public function mapToForm(Request $request, Model $model): Collection
    {
        return $this->map->toInput($request, $model)->toBase();
    }

    /**
     * Map the fields to validate.
     */
    public function mapToValidate(Request $request, Model $model): array
    {
        return $this->reduce(static function (array $rules, Field $field) use ($request, $model): array {
            return array_merge_recursive($rules, $field->toValidate($request, $model));
        }, []);
    }

    /**
     * Register the routes.
     */
    public function registerRoutes(Router $router): void
    {
        $router->prefix('fields')->group(function (Router $router): void {
            $this->each(static function (Field $field) use ($router): void {
                if ($field instanceof Routable) {
                    $field->registerRoutes($router);
                }
            });
        });
    }
}
