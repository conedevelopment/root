<?php

namespace Cone\Root\Form\Fields;

use Closure;
use Cone\Root\Http\Controllers\RepeaterController;
use Cone\Root\Traits\RegistersRoutes;
use Illuminate\Http\Request;
use Illuminate\Routing\Router;
use Illuminate\Support\Str;

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
     * Get the add new label.
     */
    public function addNewLabel(): string
    {
        return __('Add :name', ['name' => Str::singular($this->label)]);
    }

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
    public function data(Request $request): array
    {
        return array_merge(parent::data($request), [
            'addNewLabel' => $this->addNewLabel(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function routes(Router $router): void
    {
        $router->post('/', RepeaterController::class);
    }
}
