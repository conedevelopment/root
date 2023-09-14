<?php

namespace Cone\Root\Table\Actions;

use Cone\Root\Form\Form;
use Cone\Root\Interfaces\AsForm;
use Cone\Root\Support\Alert;
use Cone\Root\Support\Element;
use Cone\Root\Table\Table;
use Cone\Root\Traits\Authorizable;
use Cone\Root\Traits\Makeable;
use Illuminate\Contracts\Support\Responsable;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

abstract class Action extends Element implements AsForm, Responsable
{
    use Authorizable;
    use Makeable;

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
        return sprintf('%s-action-%s', $this->table->getAttribute('id'), $this->getKey());
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
     * Perform the action.
     */
    public function perform(Request $request): Response
    {
        $query = $this->table->resolveFilteredQuery($request);

        $this->toForm($request, $query->getModel())->handle($request);

        $this->handle(
            $request,
            $request->boolean('all') ? $query->get() : $query->findMany($request->input('models', []))
        );

        return Redirect::back()->with(
            sprintf('alerts.action-%s', $this->getKey()),
            Alert::info(__(':action was successful!', ['action' => $this->getName()]))
        );
    }

    /**
     * Convert the object to a form using the request and the model.
     */
    public function toForm(Request $request, Model $model): Form
    {
        return new ActionForm($model, '');
    }

    /**
     * {@inheritdoc}
     */
    public function toArray(): array
    {
        return array_merge(
            parent::toArray(),
            App::call(function (Request $request): array {
                return [
                    'confirmable' => $this->isConfirmable(),
                    'destructive' => $this->isDestructive(),
                    'key' => $this->getKey(),
                    'name' => $this->getName(),
                    'url' => null,
                    'modalKey' => $this->getModalKey(),
                    'form' => $this->toForm($request, $this->table->getQuery()->getModel()),
                ];
            })
        );
    }

    /**
     * Create an HTTP response that represents the object.
     *
     * @param  \Illuminate\Http\Request  $request
     */
    public function toResponse($request): Response
    {
        return match ($request->method()) {
            default => $this->perform($request),
        };
    }
}
