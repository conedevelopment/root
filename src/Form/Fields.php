<?php

namespace Cone\Root\Form;

use Cone\Root\Form\Fields\Field;
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
     * Filter the fields that are available for the current request and model.
     */
    public function authorized(Request $request, Model $model = null): static
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
     * Persist the request value on the model.
     */
    public function persist(Request $request): void
    {
        $this->each(static function (Field $field) use ($request): void {
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
        return $this->reduce(static function (array $rules, Field $field) use ($request): array {
            return array_merge_recursive($rules, $field->toValidate($request));
        }, []);
    }

    /**
     * Register the field routes.
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
