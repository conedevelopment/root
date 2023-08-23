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
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\App;
use Illuminate\Support\ViewErrorBag;
use Stringable;

class Form implements Routable, Stringable
{
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
     * The unique form key.
     */
    protected string $key;

    /**
     * Create a new form instance.
     */
    public function __construct(string $key)
    {
        $this->key = strtolower($key);
        $this->fields = new Fields($this, $this->fields());
    }

    /**
     * Get the form key.
     */
    public function getKey(): string
    {
        return $this->key;
    }

    /**
     * Get the URI key.
     */
    public function getUriKey(): string
    {
        return '';
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
            'key' => $this->getKey(),
            'method' => $this->method(),
            'url' => $this->replaceRoutePlaceholders($request->route()),
            'errors' => $this->errors($request),
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

        $this->resolveModel()->save();
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
     * Set the validation error bag.
     */
    public function errorBag(string $value): static
    {
        $this->errorBag = $value;

        return $this;
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
     * Register the routes.
     */
    public function registerRoutes(Router $router): void
    {
        $this->__registerRoutes($router);

        $this->fields->registerRoutes($router);
    }

    /**
     * Convert the form to a string.
     */
    public function __toString(): string
    {
        return $this->render()->render();
    }
}
