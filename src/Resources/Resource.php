<?php

namespace Cone\Root\Resources;

use Cone\Root\Form\Form;
use Cone\Root\Interfaces\AsForm;
use Cone\Root\Interfaces\AsTable;
use Cone\Root\Navigation\Item;
use Cone\Root\Root;
use Cone\Root\Support\Facades\Navigation;
use Cone\Root\Table\Table;
use Cone\Root\Traits\Authorizable;
use Cone\Root\Traits\ResolvesExtracts;
use Cone\Root\Traits\ResolvesWidgets;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;

class Resource implements AsForm, AsTable
{
    use Authorizable;
    use ResolvesExtracts;
    use ResolvesWidgets;

    /**
     * The model class.
     */
    protected string $model;

    /**
     * The relations to eager load on every query.
     */
    protected array $with = [];

    /**
     * The relations to eager load on every query.
     */
    protected array $withCount = [];

    /**
     * The icon for the resource.
     */
    protected string $icon = 'archive';

    /**
     * Get the model for the resource.
     */
    public function getModel(): string
    {
        return $this->model;
    }

    /**
     * Get the key.
     */
    public function getKey(): string
    {
        return Str::of($this->getModel())->classBasename()->plural()->kebab()->value();
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
        return __(Str::of($this->getModel())->classBasename()->headline()->plural()->value());
    }

    /**
     * Get the model name.
     */
    public function getModelName(): string
    {
        return __(Str::of($this->getModel())->classBasename()->value());
    }

    /**
     * Get the model instance.
     */
    public function getModelInstance(): Model
    {
        return new ($this->getModel());
    }

    /**
     * Set the resource icon.
     */
    public function icon(string $icon): static
    {
        $this->icon = $icon;

        return $this;
    }

    /**
     * Get the resource icon.
     */
    public function getIcon(): string
    {
        return $this->icon;
    }

    /**
     * Get the policy for the model.
     */
    public function getPolicy(): mixed
    {
        return Gate::getPolicyFor($this->getModel());
    }

    /**
     * Set the relations to eagerload.
     */
    public function with(array $relations): static
    {
        $this->with = $relations;

        return $this;
    }

    /**
     * Set the relation counts to eagerload.
     */
    public function withCount(array $relations): static
    {
        $this->withCount = $relations;

        return $this;
    }

    /**
     * Make a new Eloquent query instance.
     */
    public function query(): Builder
    {
        return $this->getModelInstance()->newQuery()->with($this->with)->withCount($this->withCount);
    }

    /**
     * Resolve the query for the given request.
     */
    public function resolveQuery(Request $request): Builder
    {
        return $this->query();
    }

    /**
     * Resolve the route binding query.
     */
    public function resolveRouteBindingQuery(Request $request): Builder
    {
        return $this->resolveQuery($request)->when(
            $this->isSoftDeletable(),
            static function (Builder $query): Builder {
                return $query->withTrashed();
            }
        );
    }

    /**
     * Resolve the resource model for a bound value.
     */
    public function resolveRouteBinding(Request $request, string $id): Model
    {
        return $this->resolveRouteBindingQuery($request)->findOrFail($id);
    }

    /**
     * Determine if the model soft deletable.
     */
    public function isSoftDeletable(): bool
    {
        return in_array(SoftDeletes::class, class_uses_recursive($this->getModel()));
    }

    /**
     * The URL for the given model.
     */
    public function modelUrl(Model $model): string
    {
        return URL::route($model->exists ? 'root.resource.update' : 'root.resource.store', [$this->getUriKey(), $model]);
    }

    /**
     * Make a new form for the model.
     */
    public function toTable(Request $request, Builder $query): Table
    {
        return (new Table($query))
            ->rowUrl(function (Request $request, Model $model): string {
                return $this->modelUrl($model);
            });
    }

    /**
     * Make a new form for the model.
     */
    public function toForm(Request $request, Model $model): Form
    {
        return new Form(
            $model,
            $this->modelUrl($model),
            sprintf('/root/api/%s/form/fields', $this->getKey())
        );
    }

    /**
     * Get the index representation of the resource.
     */
    public function toIndex(Request $request): array
    {
        return [
            'title' => '',
            'table' => $this->toTable($request, $this->resolveQuery($request)),
        ];
    }

    /**
     * Get the create representation of the resource.
     */
    public function toCreate(Request $request): array
    {
        return [
            //
        ];
    }

    /**
     * Get the edit representation of the resource.
     */
    public function toEdit(Request $request, Model $model): array
    {
        return [
            'title' => '',
            'form' => $this->toForm($request, $model),
        ];
    }

    /**
     * Get the navigation compatible format of the resource.
     */
    public function toNavigationItem(Request $request): Item
    {
        return Item::make('#', $this->getName())
            ->icon($this->icon);
    }

    /**
     * Handle the resource registered event.
     */
    public function boot(Root $root): void
    {
        Navigation::location('sidebar')->add($this->toNavigationItem($root->app['request']));
    }
}
