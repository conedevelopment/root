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
     * Map the fields to validate.
     */
    public function mapToValidate(Request $request, Model $model): array
    {
        return $this->reduce(static function (array $rules, Field $field) use ($request, $model): array {
            return array_merge_recursive($rules, $field->toValidate($request, $model));
        }, []);
    }

    /**
     * Map the field to form components.
     */
    public function mapToFormComponents(Request $request, Model $model): array
    {
        return $this->map->toFormComponent($request, $model)->all();
    }

    /**
     * Register the field routes.
     */
    public function registerRoutes(Request $request, Router $router): void
    {
        $router->prefix('fields')->group(function (Router $router) use ($request): void {
            $this->each(static function (Field $field) use ($request, $router): void {
                if (in_array(RegistersRoutes::class, class_uses_recursive($field))) {
                    /** @var \Tests\FieldWithRoute $field */
                    $field->registerRoutes($request, $router);
                }
            });
        });
    }
}
