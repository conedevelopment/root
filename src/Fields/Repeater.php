<?php

namespace Cone\Root\Fields;

use Closure;
use Cone\Root\Http\Controllers\RepeaterController;
use Cone\Root\Traits\RegistersRoutes;
use Cone\Root\Traits\ResolvesFields;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Str;

class Repeater extends Field
{
    use RegistersRoutes {
        RegistersRoutes::registerRoutes as __registerRoutes;
    }
    use ResolvesFields {
        ResolvesFields::withFields as __withFields;
    }

    /**
     * The Blade template.
     */
    protected string $template = 'root::fields.repeater';

    /**
     * The option fields resolver.
     */
    protected ?Closure $optionFieldsResolver = null;

    /**
     * The maximum number of options.
     */
    protected ?int $max = null;

    /**
     * Get the URI key.
     */
    public function getUriKey(): string
    {
        return str_replace('.', '-', $this->getRequestKey());
    }

    /**
     * Get the route parameter name.
     */
    public function getRouteParameterName(): string
    {
        return 'field';
    }

    /**
     * Set the maximum number of options.
     */
    public function max(int $value): static
    {
        $this->max = $value;

        return $this;
    }

    /**
     * Get the option name.
     */
    public function getOptionName(): string
    {
        return __(Str::singular($this->label));
    }

    /**
     * Get the add new option label.
     */
    public function getAddNewOptionLabel(): string
    {
        return __('Add :name', ['name' => $this->getOptionName()]);
    }

    /**
     * {@inheritdoc}
     */
    public function getValueForHydrate(Request $request): mixed
    {
        return array_values((array) parent::getValueForHydrate($request));
    }

    /**
     * {@inheritdoc}
     */
    public function getOldValue(Request $request): mixed
    {
        return array_values((array) parent::getOldValue($request));
    }

    /**
     * Handle the callback for the field resolution.
     */
    protected function resolveField(Request $request, Field $field): void
    {
        $field->setModelAttribute(
            sprintf('%s.*.%s', $this->getModelAttribute(), $field->getModelAttribute())
        );
    }

    /**
     * {@inheritdoc}
     */
    public function withFields(Closure $callback): static
    {
        $this->optionFieldsResolver = function (Request $request, Model $model, Model $tmpModel) use ($callback): Fields {
            $fields = new Fields([
                Hidden::make(__('Key'), '_key'),
            ]);

            $fields->register(call_user_func_array($callback, [$request, $model, $tmpModel]));

            $fields->each(function (Field $field) use ($tmpModel): void {
                $attribute = sprintf(
                    '%s.%s.%s',
                    $this->getModelAttribute(),
                    $tmpModel->getAttribute('_key'),
                    $key = $field->getModelAttribute()
                );

                $field->setModelAttribute($attribute)
                    ->name($attribute)
                    ->id($attribute)
                    ->value(function () use ($tmpModel, $key): mixed {
                        return $tmpModel->getAttribute($key);
                    });
            });

            return $fields;
        };

        return $this->__withFields($callback);
    }

    /**
     * Make a new temporary model for the option.
     */
    public function newTemporaryModel(array $attributes = []): Model
    {
        $model = new class() extends Model
        {
            //
        };

        return $model->forceFill(array_replace(
            ['_key' => Str::uuid()],
            $attributes
        ));
    }

    /**
     * Resolve the repeater options.
     */
    public function resolveOptions(Request $request, Model $model): array
    {
        $value = (array) $this->resolveValue($request, $model);

        return array_map(function (array $option) use ($request, $model): array {
            return $this->toOption($request, $model, $this->newTemporaryModel($option));
        }, $value);
    }

    /**
     * Build a new option.
     */
    public function buildOption(Request $request, Model $model): array
    {
        $option = $this->toOption($request, $model, $this->newTemporaryModel([]));

        $option['fields'] = $option['fields']->mapToInputs($request, $model);

        $option['html'] = View::make('root::fields.repeater-option', $option)->render();

        return $option;
    }

    /**
     * Register the routes using the given router.
     */
    public function registerRoutes(Request $request, Router $router): void
    {
        $this->__registerRoutes($request, $router);

        $router->prefix($this->getUriKey())->group(function (Router $router) use ($request): void {
            $this->resolveFields($request)->registerRoutes($request, $router);
        });
    }

    /**
     * The routes that should be registered.
     */
    public function routes(Router $router): void
    {
        $router->post('/', RepeaterController::class);
    }

    /**
     * Get the option representation of the model and the temporary model.
     */
    public function toOption(Request $request, Model $model, Model $tmpModel): array
    {
        return [
            'open' => true,
            'value' => $tmpModel->getAttribute('_key'),
            'label' => $this->getOptionName(),
            'fields' => is_null($this->optionFieldsResolver)
                ? new Fields()
                : call_user_func_array($this->optionFieldsResolver, [$request, $model, $tmpModel]),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function toInput(Request $request, Model $model): array
    {
        return array_merge(parent::toInput($request, $model), [
            'addNewLabel' => $this->getAddNewOptionLabel(),
            'max' => $this->max,
            'options' => array_map(static function (array $option) use ($request, $model): array {
                $option['fields'] = $option['fields']->mapToInputs($request, $model);

                return array_merge($option, [
                    'html' => View::make('root::fields.repeater-option', $option)->render(),
                ]);
            }, $this->resolveOptions($request, $model)),
            'url' => $this->replaceRoutePlaceholders($request->route()),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function toValidate(Request $request, Model $model): array
    {
        return array_merge(
            parent::toValidate($request, $model),
            $this->resolveFields($request)->mapToValidate($request, $model)
        );
    }
}
