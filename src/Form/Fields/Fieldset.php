<?php

namespace Cone\Root\Form\Fields;

use Cone\Root\Interfaces\Routable;
use Cone\Root\Traits\RegistersRoutes;
use Cone\Root\Traits\ResolvesFields;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Routing\Router;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\App;

class Fieldset extends Field implements Routable
{
    use ResolvesFields;
    use RegistersRoutes {
        RegistersRoutes::registerRoutes as __registerRoutes;
    }

    /**
     * The Vue component.
     */
    protected string $component = 'Fieldset';

    /**
     * Register the routes using the given router.
     */
    public function registerRoutes(Router $router): void
    {
        $request = App::make('request');

        $router->prefix($this->getUriKey())->group(function (Router $router) use ($request): void {
            $this->resolveFields($request)->registerRoutes($router);
        });
    }

    /**
     * {@inheritdoc}
     */
    public function persist(Request $request, Model $model, mixed $value): void
    {
        $this->resolveFields($request)
            ->authorized($request, $model)
            ->each(static function (Field $field) use ($request, $model, $value): void {
                $field->persist($request, $model, $value[$field->getKey()] ?? null);
            });
    }

    /**
     * {@inheritdoc}
     */
    public function resolveHydrate(Request $request, Model $model, mixed $value): void
    {
        $this->resolveFields($request)
            ->authorized($request, $model)
            ->each(static function (Field $field) use ($request, $model, $value): void {
                $field->resolveHydrate($request, $model, $value[$field->getKey()] ?? null);
            });
    }

    /**
     * {@inheritdoc}
     */
    public function toInput(Request $request, Model $model): array
    {
        $fields = $this->resolveFields($request)
            ->authorized($request, $model)
            ->mapToForm($request, $model);

        return array_replace_recursive(parent::toInput($request, $model), [
            'fields' => $fields,
            'formattedValue' => array_column($fields, 'formattedValue', 'name'),
            'value' => array_column($fields, 'value', 'name'),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function toValidate(Request $request, Model $model): array
    {
        $rules = $this->resolveFields($request)
            ->authorized($request, $model)
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
