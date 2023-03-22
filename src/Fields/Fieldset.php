<?php

namespace Cone\Root\Fields;

use Cone\Root\Http\Requests\RootRequest;
use Cone\Root\Traits\RegistersRoutes;
use Cone\Root\Traits\ResolvesFields;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Routing\Router;
use Illuminate\Support\Collection;

class Fieldset extends Field
{
    use ResolvesFields;
    use RegistersRoutes {
        RegistersRoutes::registerRoutes as defaultRegisterRoutes;
    }

    /**
     * The Vue component.
     */
    protected string $component = 'Fieldset';

    /**
     * Handle the resolving event on the field instance.
     */
    protected function resolveField(RootRequest $request, Field $field): void
    {
        $field->mergeAuthorizationResolver(function (...$parameters): bool {
            return $this->authorized(...$parameters);
        });
    }

    /**
     * Register the routes using the given router.
     */
    public function registerRoutes(RootRequest $request, Router $router): void
    {
        $router->prefix($this->getKey())->group(function (Router $router) use ($request): void {
            $this->resolveFields($request)->registerRoutes($request, $router);
        });
    }

    /**
     * {@inheritdoc}
     */
    public function persist(RootRequest $request, Model $model): void
    {
        $this->resolveHydrate($request, $model, $this->getValueForHydrate($request, $model));

        $this->resolveFields($request)
            ->available($request, $model)
            ->each(static function (Field $field) use ($request, $model): void {
                $field->persist($request, $model);
            });
    }

    /**
     * {@inheritdoc}
     */
    public function resolveHydrate(RootRequest $request, Model $model, mixed $value): void
    {
        $this->resolveFields($request)
            ->available($request, $model)
            ->each(static function (Field $field) use ($request, $model, $value): void {
                $field->resolveHydrate($request, $model, $value[$field->getKey()] ?? null);
            });
    }

    /**
     * {@inheritdoc}
     */
    public function toInput(RootRequest $request, Model $model): array
    {
        $fields = $this->resolveFields($request)
                    ->available($request, $model)
                    ->mapToForm($request, $model)
                    ->toArray();

        return array_replace_recursive(parent::toInput($request, $model), [
            'fields' => $fields,
            'formatted_value' => array_column($fields, 'formatted_value', 'name'),
            'value' => array_column($fields, 'value', 'name'),
        ]);
    }

    /**
     * Get the validation representation of the field.
     */
    public function toValidate(RootRequest $request, Model $model): array
    {
        $rules = $this->resolveFields($request)
                    ->available($request, $model)
                    ->mapToValidate($request, $model);

        return array_merge(
            parent::toValidate($request, $model),
            Collection::make($rules)
                    ->mapWithKeys(function (array $rules, string $key): array {
                        return [sprintf('%s.%s', $this->getKey(), $key) => $rules];
                    })
                    ->toArray(),
        );
    }
}
