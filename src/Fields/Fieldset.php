<?php

namespace Cone\Root\Fields;

use Closure;
use Cone\Root\Traits\RegistersRoutes;
use Cone\Root\Traits\ResolvesFields;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Routing\Router;
use Illuminate\Support\Str;

class Fieldset extends Field
{
    use RegistersRoutes;
    use ResolvesFields;

    /**
     * The Blade template.
     */
    protected string $template = 'root::fields.fieldset';

    /**
     * Create a new field instance.
     */
    public function __construct(string $label, Closure|string|null $modelAttribute = null)
    {
        parent::__construct($label, $modelAttribute);

        $this->hiddenOn(['index', 'show']);
    }

    /**
     * Get the URI key.
     */
    public function getUriKey(): string
    {
        return str_replace('.', '-', $this->getRequestKey());
    }

    /**
     * Handle the callback for the field resolution.
     */
    protected function resolveField(Request $request, Field $field): void
    {
        $field->setAttribute('form', $this->getAttribute('form'));
        $field->resolveErrorsUsing($this->errorsResolver);

        if ($field instanceof Relation) {
            $field->resolveRouteKeyNameUsing(function () use ($field): string {
                return Str::of($field->getRelationName())->singular()->ucfirst()->prepend($this->getModelAttribute())->value();
            });
        }
    }

    /**
     * Register the routes using the given router.
     */
    public function registerRoutes(Request $request, Router $router): void
    {
        $router->prefix($this->getUriKey())->group(function (Router $router) use ($request): void {
            $this->resolveFields($request)->registerRoutes($request, $router);
        });
    }

    /**
     * {@inheritdoc}
     */
    public function persist(Request $request, Model $model, mixed $value): void
    {
        $this->resolveFields($request)->each(static function (Field $field) use ($request, $model): void {
            $field->persist($request, $model, $field->getValueForHydrate($request));
        });
    }

    /**
     * {@inheritdoc}
     */
    public function resolveHydrate(Request $request, Model $model, mixed $value): void
    {
        $this->resolveFields($request)->each(static function (Field $field) use ($request, $model): void {
            $field->resolveHydrate($request, $model, $field->getValueForHydrate($request));
        });
    }

    /**
     * {@inheritdoc}
     */
    public function invalid(Request $request): bool
    {
        return parent::invalid($request)
            || $this->resolveFields($request)->some(fn (Field $field): bool => $field->invalid($request));
    }

    /**
     * {@inheritdoc}
     */
    public function toInput(Request $request, Model $model): array
    {
        return array_merge(parent::toInput($request, $model), [
            'fields' => $this->resolveFields($request)->mapToInputs($request, $model),
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
