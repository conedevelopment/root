<?php

namespace Cone\Root\Form\Fields;

use Cone\Root\Form\Form;
use Cone\Root\Interfaces\Routable;
use Cone\Root\Traits\RegistersRoutes;
use Cone\Root\Traits\ResolvesFields;
use Illuminate\Http\Request;
use Illuminate\Routing\Router;

class Fieldset extends Field implements Routable
{
    use ResolvesFields;
    use RegistersRoutes {
        RegistersRoutes::registerRoutes as __registerRoutes;
    }

    /**
     * Create a new field instance.
     */
    public function __construct(Form $form, string $label, string $key = null)
    {
        parent::__construct($form, $label, $key);

        $this->fields = new Fields($form, $this->fields());
    }

    /**
     * The Blade template.
     */
    protected string $template = 'root::form.fields.fieldset';

    /**
     * {@inheritdoc}
     */
    public function data(Request $request): array
    {
        return array_merge(parent::data($request), [
            'fields' => $this->fields->all(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function persist(Request $request, mixed $value): void
    {
        $this->fields->each(static function (Field $field) use ($request): void {
            $field->persist($request, $field->getValueForHydrate($request));
        });
    }

    /**
     * {@inheritdoc}
     */
    public function resolveHydrate(Request $request, mixed $value): void
    {
        $this->fields->each(static function (Field $field) use ($request): void {
            $field->resolveHydrate($request, $field->getValueForHydrate($request));
        });
    }

    /**
     * {@inheritdoc}
     */
    public function invalid(Request $request): bool
    {
        return parent::invalid($request)
            || $this->fields->some(fn (Field $field): bool => $field->invalid($request));
    }

    /**
     * Register the routes using the given router.
     */
    public function registerRoutes(Router $router): void
    {
        $this->__registerRoutes($router);

        $router->prefix($this->getUriKey())->group(function (Router $router): void {
            $this->fields->registerRoutes($router);
        });
    }

    /**
     * {@inheritdoc}
     */
    public function toValidate(Request $request): array
    {
        return array_merge(
            parent::toValidate($request),
            $this->fields->mapToValidate($request)
        );
    }
}
