<?php

namespace Cone\Root\Form\Fields;

use Cone\Root\Form\Form;
use Cone\Root\Traits\RegistersRoutes;
use Cone\Root\Traits\ResolvesFields;
use Illuminate\Http\Request;
use Illuminate\Routing\Router;

class Json extends Field
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
     * Create a new method.
     */
    public function data(Request $request): array
    {
        return array_merge(parent::data($request), [
            'fields' => $this->fields->all(),
        ]);
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
}
