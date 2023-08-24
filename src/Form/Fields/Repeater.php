<?php

namespace Cone\Root\Form\Fields;

use Closure;
use Cone\Root\Http\Controllers\RepeaterController;
use Cone\Root\Traits\RegistersRoutes;
use Illuminate\Routing\Router;

class Repeater extends Fieldset
{
    use RegistersRoutes {
        RegistersRoutes::registerRoutes as __registerRoutes;
    }

    /**
     * The Blade template.
     */
    protected string $template = 'root::form.fields.repeater';

    /**
     * {@inheritdoc}
     */
    public function withFields(Closure $callback): static
    {
        //

        return parent::withFields($callback);
    }

    /**
     * {@inheritdoc}
     */
    public function routes(Router $router): void
    {
        $router->post('/', [RepeaterController::class]);
    }
}
