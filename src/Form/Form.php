<?php

namespace Cone\Root\Form;

use Closure;
use Cone\Root\Form\Fields\Fields;
use Cone\Root\Interfaces\Routable;
use Cone\Root\Traits\Makeable;
use Cone\Root\Traits\RegistersRoutes;
use Cone\Root\Traits\ResolvesFields;
use Exception;
use Illuminate\Contracts\Support\MessageBag;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Traits\Macroable;
use Illuminate\Support\ViewErrorBag;

class Form implements Renderable, Routable
{
    use Macroable;
    use Makeable;
    use ResolvesFields;
    use RegistersRoutes {
        RegistersRoutes::registerRoutes as __registerRoutes;
    }

    /**
     * The Blade template.
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
     * The error bag.
     */
    protected string $errorBag = 'default';

    /**
     * The form errors.
     */
    protected ?MessageBag $errors = null;

    /**
     * Create a new form instance.
     */
    public function __construct()
    {
        $this->fields = new Fields($this, $this->fields());
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
     * Get the view data.
     */
    public function data(Request $request): array
    {
        return [
            'fields' => $this->fields->all(),
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
            $this->template,
            App::call([$this, 'data'])
        );
    }

    /**
     * Handle the incoming form request.
     */
    public function handle(Request $request): void
    {
        $this->validate($request);

        $this->fields->persist($request);
    }

    /**
     * Validate the incoming request.
     */
    public function validate(Request $request): array
    {
        return $request->validateWithBag(
            $this->errorBag,
            $this->fields->mapToValidate($request)
        );
    }

    /**
     * Get the errors for the form.
     */
    public function errors(Request $request): MessageBag
    {
        if (is_null($this->errors)) {
            $this->errors = $request->session()->get('errors', new ViewErrorBag())->getBag($this->errorBag);
        }

        return $this->errors;
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

        $this->fields->registerRoutes($router);
    }
}
