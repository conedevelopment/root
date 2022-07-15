<?php

namespace Cone\Root\Fields;

use Cone\Root\Http\Requests\RootRequest;
use Cone\Root\Traits\RegistersRoutes;
use Cone\Root\Traits\ResolvesFields;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Routing\Router;

class Fieldset extends Field
{
    use ResolvesFields;
    use RegistersRoutes {
        RegistersRoutes::registerRoutes as defaultRegisterRoutes;
    }

    /**
     * The Vue component.
     *
     * @var string
     */
    protected string $component = 'Fieldset';

    /**
     * {@inheritdoc}
     */
    public function resolveValue(RootRequest $request, Model $model): mixed
    {
        return null;
    }

    /**
     * Handle the resolving event on the field instance.
     *
     * @param  \Cone\Root\Http\Requests\RootRequest  $request
     * @param  \Cone\Root\Fields\Field  $field
     * @return void
     */
    protected function resolveField(RootRequest $request, Field $field): void
    {
        $field->mergeAuthorizationResolver(function (...$parameters): bool {
            return $this->authorized(...$parameters);
        });
    }

    /**
     * Register the routes using the given router.
     *
     * @param  \Cone\Root\Http\Requests\RootRequest  $request
     * @param  \Illuminate\Routing\Router  $router
     * @return void
     */
    public function registerRoutes(RootRequest $request, Router $router): void
    {
        $this->defaultRegisterRoutes($request, $router);

        $router->prefix($this->getKey())->group(function (Router $router) use ($request): void {
            $this->resolveFields($request)->registerRoutes($request, $router);
        });
    }

    /**
     * {@inheritdoc}
     */
    public function toInput(RootRequest $request, Model $model): array
    {
        return array_merge(parent::toInput($request, $model), [
            'fields' => $this->resolveFields($request)
                            ->available($request, $model)
                            ->mapToForm($request, $model)
                            ->toArray(),
        ]);
    }

    /**
     * Get the validation representation of the field.
     *
     * @param  \Cone\Root\Http\Requests\RootRequest  $request
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @return array
     */
    public function toValidate(RootRequest $request, Model $model): array
    {
        return $this->resolveFields($request)
                    ->available($request, $model)
                    ->mapToValidate($request, $model);
    }
}
