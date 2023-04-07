<?php

namespace Cone\Root\Traits;

use Closure;
use Cone\Root\Relations\Relation;
use Cone\Root\Support\Collections\Relations;
use Illuminate\Http\Request;

trait ResolvesRelations
{
    /**
     * The relations resolver callback.
     */
    protected ?Closure $relationsResolver = null;

    /**
     * The resolved relations.
     */
    protected ?Relations $relations = null;

    /**
     * Define the relations for the object.
     */
    public function relations(Request $request): array
    {
        return [];
    }

    /**
     * Set the relations resolver.
     */
    public function withRelations(array|Closure $relations): static
    {
        $this->relationsResolver = is_array($relations) ? fn (): array => $relations : $relations;

        return $this;
    }

    /**
     * Resolve the relations.
     */
    public function resolveRelations(Request $request): Relations
    {
        if (is_null($this->relations)) {
            $this->relations = Relations::make()->register($this->relations($request));

            if (! is_null($this->relationsResolver)) {
                $this->relations->register(call_user_func_array($this->relationsResolver, [$request]));
            }

            $this->relations->each(function (Relation $relation) use ($request): void {
                $this->resolveRelation($request, $relation);
            });
        }

        return $this->relations;
    }

    /**
     * Handle the resolving event on the relation instance.
     */
    protected function resolveRelation(Request $request, Relation $relation): void
    {
        //
    }
}
