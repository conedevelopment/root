<?php

namespace Cone\Root\Fields;

use Closure;
use Cone\Root\Http\Controllers\RelationController;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Routing\Router;

class Events extends MorphMany
{
    /**
     * Indicates whether the relation is a sub resource.
     */
    protected bool $asSubResource = true;

    /**
     * The relations to eager load on every query.
     */
    protected array $with = [
        'user',
        'target',
    ];

    /**
     * Create a new relation field instance.
     */
    public function __construct(?string $label = null, Closure|string|null $modelAttribute = 'rootEvents', Closure|string|null $relation = null)
    {
        parent::__construct($label ?: __('Events'), $modelAttribute, $relation);
    }

    /**
     * {@inheritdoc}
     */
    public function persist(Request $request, Model $model, mixed $value): void
    {
        //
    }

    /**
     * {@inheritdoc}
     */
    public function resolveHydrate(Request $request, Model $model, mixed $value): void
    {
        //
    }

    /**
     * {@inheritdoc}
     */
    public function routes(Router $router): void
    {
        $router->get('/', [RelationController::class, 'index']);
        $router->get("/{{$this->getRouteKeyName()}}", [RelationController::class, 'show']);
    }

    /**
     * {@inheritdoc}
     */
    public function mapRelationAbilities(Request $request, Model $model): array
    {
        return [
            'viewAny' => $this->resolveAbility('viewAny', $request, $model),
            'create' => false,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function mapRelatedAbilities(Request $request, Model $model, Model $related): array
    {
        return [
            'view' => $this->resolveAbility('view', $request, $model, $related),
            'update' => false,
            'restore' => false,
            'delete' => false,
            'forceDelete' => false,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function fields(Request $request): array
    {
        return [
            ID::make()->sortable(),

            Text::make(__('Action'), 'action')->sortable(),

            BelongsTo::make(__('User'), 'user')
                ->display('name'),

            Date::make(__('Date'), 'created_at')
                ->withTime(),
        ];
    }
}
