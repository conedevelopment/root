<?php

namespace Cone\Root\Fields;

use Cone\Root\Models\TemporaryJson;
use Cone\Root\Traits\RegistersRoutes;
use Cone\Root\Traits\ResolvesFields;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Routing\Router;
use Illuminate\Support\Collection;

class Json extends Field
{
    use ResolvesFields;
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
     * Indicates of the fieldset legend is visible.
     *
     * @var bool
     */
    protected bool $withLegend = true;

    /**
     * Set the with legend attribute.
     *
     * @return $this
     */
    public function withoutLegend(): static
    {
        $this->widthLegend = false;

        return $this;
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
            'fields' => $fields,
            'formatted_value' => array_column($fields, 'formatted_value', 'name'),
            'value' => array_column($fields, 'value', 'name'),
            'with_legend' => $this->withLegend,
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
                        return [sprintf('%s.%s', $this->name, $key) => $rules];
                    })
                    ->toArray(),
        );
    }
}
