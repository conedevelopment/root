<?php

namespace Cone\Root\Fields;

use Cone\Root\Traits\RegistersRoutes;
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
     * Persist the request value on the model.
     */
    public function persist(Request $request, Model $model): void
    {
        $this->each(static function (Field $field) use ($request, $model): void {
            $field->persist(
                $request, $model, $field->getValueForHydrate($request)
            );
        });
    }

    /**
     * Filter the fields that are available for the current request and model.
     */
    public function authorized(Request $request, ?Model $model = null): static
    {
        return $this->filter->authorized($request, $model)->values();
    }

    /**
     * Filter the fields that are visible in the given context.
     */
    public function visible(string|array $context): static
    {
        return $this->filter->visible($context)->values();
    }

    /**
     * Filter the searchable fields.
     */
    public function searchable(): static
    {
        return $this->filter->isSearchable();
    }

    /**
     * Filter the sortable fields.
     */
    public function sortable(): static
    {
        return $this->filter->isSortable();
    }

    /**
     * Filter the filterable fields.
     */
    public function filterable(): static
    {
        return $this->filter->isFilterable();
    }

    /**
     * Filter the relation fields.
     */
    public function relation(): static
    {
        return $this->filter(static fn (Field $field): bool => $field instanceof Relation);
    }

    /**
     * Filter the translatable fields.
     */
    public function translatable(): static
    {
        return $this->filter(static fn (Field $field): bool => $field->isTranslatable());
    }

    /**
     * Filter the subresource fields.
     */
    public function subResource(bool $value = true): static
    {
        return $this->filter(static fn (Field $field): bool => $value
            ? $field instanceof Relation && $field->isSubResource()
            : ! $field instanceof Relation || ! $field->isSubResource());
    }

    /**
     * Map the fields to validate.
     */
    public function mapToValidate(Request $request, Model $model): array
    {
        return $this->reduce(static fn (array $rules, Field $field): array => array_merge_recursive($rules, $field->toValidate($request, $model)), []);
    }

    /**
     * Map the fields to displayable data.
     */
    public function mapToDisplay(Request $request, Model $model): array
    {
        return $this->map->toDisplay($request, $model)->filter()->values()->all();
    }

    /**
     * Map the fields to form inputs.
     */
    public function mapToInputs(Request $request, Model $model): array
    {
        return $this->map->toInput($request, $model)->filter()->values()->all();
    }

    /**
     * Register the field routes.
     */
    public function registerRoutes(Request $request, Router $router): void
    {
        $router->prefix('fields')->group(function (Router $router) use ($request): void {
            $this->each(static function (Field $field) use ($request, $router): void {
                if (in_array(RegistersRoutes::class, class_uses_recursive($field))) {
                    /** @var \Cone\Root\Tests\phpstan\FieldWithRoute $field */
                    $field->registerRoutes($request, $router);
                }
            });
        });
    }
}
