<?php

namespace Cone\Root\Fields;

use Closure;
use Cone\Root\Models\TemporaryJson;
use Cone\Root\Support\Collections\Fields;
use Cone\Root\Traits\RegistersRoutes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Routing\Router;
use Illuminate\Support\Collection;

class Json extends Field
{
    use RegistersRoutes {
        RegistersRoutes::registerRoutes as defaultRegisterRotues;
    }

    /**
     * The Vue compoent.
     *
     * @var string
     */
    protected string $component = 'Json';

    /**
     * The fields resolver callback.
     *
     * @var \Closure|null
     */
    protected ?Closure $fieldsResolver = null;

    /**
     * The resolved components.
     *
     * @var array
     */
    protected array $resolved = [];

    /**
     * Set the fields resolver.
     *
     * @param  array|\Closure  $fields
     * @return $this
     */
    public function withFields(array|Closure $fields): static
    {
        if (is_array($fields)) {
            $fields = static function () use ($fields): array {
                return $fields;
            };
        }

        $this->fieldsResolver = $fields;

        return $this;
    }

    /**
     * Resolve fields.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Cone\Root\Support\Collections\Fields
     */
    public function resolveFields(Request $request): Fields
    {
        if (! isset($this->resolved['fields'])) {
            $fields = Fields::make();

            if (! is_null($this->fieldsResolver)) {
                $resolved = call_user_func_array($this->fieldsResolver, [$request]);

                $fields = $fields->merge($resolved);
            }

            $this->resolved['fields'] = $fields->each->mergeAuthorizationResolver(function (Request $request): bool {
                return $this->authorized($request);
            });
        }

        return $this->resolved['fields'];
    }

    /**
     * Register the field routes.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Illuminate\Routing\Router  $router
     * @return void
     */
    public function registerRoutes(Request $request, Router $router): void
    {
        $this->defaultRegisterRotues($request, $router);

        $router->prefix($this->getKey())->group(function (Router $router) use ($request): void {
            $this->resolveFields($request)->registerRoutes($request, $router);
        });
    }

    /**
     * The routes that should be registerd.
     *
     * @param  \Illuminate\Routing\Router  $router
     * @return void
     */
    public function routes(Router $router): void
    {
        //
    }

    /**
     * {@inheritdoc}
     */
    public function toInput(Request $request, Model $model): array
    {
        $data = parent::toInput($request, $model);

        $data['value'] = (array) $data['value'];

        $json = TemporaryJson::make()
                            ->setRelation('parent', $model)
                            ->forceFill($data['value']);

        $fields = $this->resolveFields($request)
                    ->available($request, $model)
                    ->mapToForm($request, $json)
                    ->toArray();

        return array_replace_recursive($data, [
            'value' => array_column($fields, 'value', 'name'),
            'formatted_value' => array_column($fields, 'formatted_value', 'name'),
            'fields' => $fields,
        ]);
    }

    /**
     * Get the validation representation of the field.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @return array
     */
    public function toValidate(Request $request, Model $model): array
    {
        $fieldRules = $this->resolveFields($request)
                            ->available($request, $model)
                            ->mapToValidate($request, $model);

        return array_merge(
            parent::toValidate($request, $model),
            Collection::make($fieldRules)
                    ->mapWithKeys(function (array $rules, string $key): array {
                        return [$this->name.'.'.$key => $rules];
                    })
                    ->toArray(),
        );
    }
}
