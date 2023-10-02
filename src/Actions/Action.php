<?php

namespace Cone\Root\Actions;

use Cone\Root\Fields\Field;
use Cone\Root\Interfaces\Form;
use Cone\Root\Support\Alert;
use Cone\Root\Support\Element;
use Cone\Root\Traits\AsForm;
use Cone\Root\Traits\Authorizable;
use Cone\Root\Traits\Makeable;
use Illuminate\Contracts\Support\Responsable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

abstract class Action extends Element implements Form, Responsable
{
    use AsForm;
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
     * The Eloquent query.
     */
    protected ?Builder $query = null;

    /**
     * The API URI.
     */
    protected ?string $apiUri = null;

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
        return sprintf('action-%s', $this->getKey());
    }

    /**
     * Set the API URI.
     */
    public function setApiUri(string $apiUri): static
    {
        $this->apiUri = $apiUri;

        return $this;
    }

    /**
     * Get the API URI.
     */
    public function getApiUri(): ?string
    {
        return $this->apiUri;
    }

    /**
     * Set the Eloquent query.
     */
    public function setQuery(Builder $query): static
    {
        $this->query = $query;

        return $this;
    }

    /**
     * Handle the callback for the field resolution.
     */
    protected function resolveField(Request $request, Field $field): void
    {
        $field->setForm($this);
        $field->setApiUri(sprintf('/%s/fields/%s', $this->getApiUri(), $field->getUriKey()));
        $field->setAttribute('form', $this->getKey());
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
        $this->validateFormRequest($request);

        $this->handle(
            $request,
            $request->boolean('all') ? $this->query->get() : $this->query->findMany($request->input('models', []))
        );

        return Redirect::back()->with(
            sprintf('alerts.action-%s', $this->getKey()),
            Alert::info(__(':action was successful!', ['action' => $this->getName()]))
        );
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
                    'url' => $this->getApiUri(),
                    'modalKey' => $this->getModalKey(),
                    'fields' => $this->resolveFields($request)
                        ->each(function (Field $field): void {
                            $field->setModel($this->query->getModel());
                        })
                        ->all(),
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
        return $this->perform($request);
    }
}
