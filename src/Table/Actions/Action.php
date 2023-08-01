<?php

namespace Cone\Root\Table\Actions;

use Cone\Root\Http\Controllers\ActionController;
use Cone\Root\Interfaces\Routable;
use Cone\Root\Support\Alert;
use Cone\Root\Table\Table;
use Cone\Root\Traits\AsForm;
use Cone\Root\Traits\Authorizable;
use Cone\Root\Traits\Makeable;
use Cone\Root\Traits\RegistersRoutes;
use Cone\Root\Traits\ResolvesQuery;
use Illuminate\Contracts\Support\Responsable;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Str;
use Stringable;
use Symfony\Component\HttpFoundation\Response;

abstract class Action implements Responsable, Routable, Stringable
{
    use AsForm;
    use Authorizable;
    use Makeable;
    use ResolvesQuery;
    use RegistersRoutes {
        RegistersRoutes::registerRoutes as __registerRoutes;
    }

    /**
     * The Blade template.
     */
    protected string $template = 'root::table.actions.action';

    /**
     * Indicates if the action is descrtuctive.
     */
    protected bool $destructive = false;

    /**
     * Indicates if the action is confirmable.
     */
    protected bool $confirmable = false;

    /**
     * The table instance.
     */
    protected Table $table;

    /**
     * Create a new action instance.
     */
    public function __construct(Table $table)
    {
        $this->table = $table;
    }

    /**
     * Handle the action.
     */
    abstract public function handle(Request $request, Collection $models): void;

    /**
     * Get the key.
     */
    public function getKey(): string
    {
        return Str::of(static::class)->classBasename()->kebab()->value();
    }

    /**
     * Get the URI key.
     */
    public function getUriKey(): string
    {
        return $this->getKey();
    }

    /**
     * Get the name.
     */
    public function getName(): string
    {
        return __(Str::of(static::class)->classBasename()->headline()->value());
    }

    /**
     * Get the modal key.
     */
    public function getModalKey(): string
    {
        return sprintf('%s-action-%s', $this->table->getKey(), $this->getKey());
    }

    /**
     * Set the destructive property.
     */
    public function destructive(bool $value = true): static
    {
        $this->destructive = $value;

        return $this;
    }

    /**
     * Determine if the action is destructive.
     */
    public function isDestructive(): bool
    {
        return $this->destructive;
    }

    /**
     * Set the confirmable property.
     */
    public function confirmable(bool $value = true): static
    {
        $this->confirmable = $value;

        return $this;
    }

    /**
     * Determine if the action is confirmable.
     */
    public function isConfirmable(): bool
    {
        return $this->confirmable;
    }

    /**
     * Resolve the query.
     */
    public function resolveQuery(Request $request): Builder
    {
        return $this->table->resolveFilteredQuery($request);
    }

    /**
     * Perform the action.
     */
    public function perform(Request $request): Response
    {
        $query = $this->resolveQuery($request);

        $this->form($request)->handle($request);

        $this->handle(
            $request,
            $request->boolean('all') ? $query->get() : $query->findMany($request->input('models', []))
        );

        return $this->toResponse($request);
    }

    /**
     * Register the action routes.
     */
    public function registerRoutes(Router $router): void
    {
        $this->__registerRoutes($router);

        $request = App::make('request');

        $router->prefix($this->getUriKey())->group(function (Router $router) use ($request): void {
            $this->form($request)->registerRoutes($router);
        });
    }

    /**
     * The routes that should be registered.
     */
    public function routes(Router $router): void
    {
        $router->post('/', ActionController::class);
    }

    /**
     * Get the view data.
     */
    public function data(Request $request): array
    {
        return [
            'confirmable' => $this->isConfirmable(),
            'destructive' => $this->isDestructive(),
            'key' => $this->getKey(),
            'name' => $this->getName(),
            'url' => $this->getUri(),
            'modalKey' => $this->getModalKey(),
        ];
    }

    /**
     * Render the action.
     */
    public function render(): View
    {
        return App::make('view')->make(
            $this->template,
            App::call([$this, 'data'])
        );
    }

    /**
     * Convert the action to a string.
     */
    public function __toString(): string
    {
        return $this->render()->render();
    }

    /**
     * Get the form representation of the action.
     */
    public function toForm(Request $request): ActionForm
    {
        return ActionForm::make()->model(function () use ($request): Model {
            return $this->table->resolveQuery($request)->getModel();
        });
    }

    /**
     * Create an HTTP response that represents the object.
     *
     * @param  \Illuminate\Http\Request  $request
     */
    public function toResponse($request): Response
    {
        return Redirect::back()->with(
            sprintf('alerts.action-%s', $this->getKey()),
            Alert::info(__(':action was successful!', ['action' => $this->getName()]))
        );
    }
}
