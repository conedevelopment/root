<?php

namespace Cone\Root\Form;

use Closure;
use Cone\Root\Form\Fields\Text;
use Cone\Root\Interfaces\Renderable;
use Cone\Root\Interfaces\Routable;
use Cone\Root\Traits\Makeable;
use Cone\Root\Traits\RegistersRoutes;
use Cone\Root\Traits\ResolvesFields;
use Exception;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\App;

class Form implements Renderable, Routable
{
    use Makeable;
    use ResolvesFields {
        ResolvesFields::resolveFields as __resolveFields;
    }
    use RegistersRoutes {
        RegistersRoutes::registerRoutes as __registerRoutes;
    }

    /**
     * The blade tempalte.
     */
    protected string $template = 'root::form.form';

    /**
     * The form model.
     */
    protected ?Model $model = null;

    /**
     * The model resolver.
     */
    protected ?Closure $modelResolver = null;

    /**
     * Resolve the fields.
     */
    public function resolveFields(Request $request): Fields
    {
        if (is_null($this->fields) && ! is_null($this->fieldsResolver)) {
            $callback = $this->fieldsResolver;

            $this->fieldsResolver = function () use ($request, $callback) {
                return call_user_func_array($callback, [$this, $request]);
            };
        }

        return $this->__resolveFields($request);
    }

    /**
     * Make a new text field.
     */
    public function textField(string $label, ?string $name = null): Text
    {
        return new Text($this, $label, $name);
    }

    /**
     * Set the model resolver callback.
     */
    public function model(Closure $callback): static
    {
        $this->model = null;

        $this->modelResolver = $callback;

        return $this;
    }

    /**
     * Resolve the model.
     */
    public function resolveModel(): Model
    {
        if (is_null($this->model) && is_null($this->modelResolver)) {
            throw new Exception();
        } elseif (is_null($this->model)) {
            $this->model = call_user_func($this->modelResolver);
        }

        return $this->model;
    }

    /**
     * Get the blade template.
     */
    public function template(): string
    {
        return $this->template;
    }

    /**
     * Get the view data.
     */
    public function data(Request $request): array
    {
        return [
            'fields' => $this->resolveFields($request),
            'url' => $this->replaceRoutePlaceholders($request->route()),
            'method' => $this->method(),
        ];
    }

    /**
     * Get the form method.
     */
    public function method(): string
    {
        return $this->resolveModel()->exists ? 'PATCH' : 'POST';
    }

    /**
     * Render the table.
     */
    public function render(): View
    {
        return App::make('view')->make(
            $this->template(),
            App::call([$this, 'data'])
        );
    }

    /**
     * Validate the incoming request.
     */
    public function validate(Request $request): array
    {
        $model = $this->resolveModel();

        //

        return [];
    }

    /**
     * Get the URI key.
     */
    public function getUriKey(): string
    {
        return '';
    }

    /**
     * Register the routes.
     */
    public function registerRoutes(Router $router): void
    {
        $this->__registerRoutes($router);

        $this->resolveFields(App::make('request'))->registerRoutes($router);
    }
}
