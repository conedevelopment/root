<?php

namespace Cone\Root\Form\Fields;

use Closure;
use Cone\Root\Models\Medium;
use Illuminate\Http\Request;

class File extends MorphMany
{
    /**
     * The storage resolver callback.
     */
    protected ?Closure $storageResolver = null;

    /**
     * Set the "multiple" HTML attribute.
     */
    public function multiple(bool $value = true): static
    {
        $this->setAttribute('multiple', $value);

        return $this;
    }

    /**
     * Set the storing resolver callback.
     */
    public function storeUsing(Closure $callback): static
    {
        $this->storageResolver = $callback;

        return $this;
    }

    /**
     * Store the file using the given path and request.
     */
    public function store(Request $request, string $path): Medium
    {
        $medium = (Medium::proxy())::makeFrom($path);

        if (! is_null($this->storageResolver)) {
            call_user_func_array($this->storageResolver, [$request, $medium, $path]);
        }

        $request->user()->uploads()->save($medium);

        return $medium;
    }

    /**
     * {@inheritdoc}
     */
    public function persist(Request $request, mixed $value): void
    {
        $this->resolveModel()->saved(function () use ($request, $value): void {
            $this->resolveHydrate($request, $value);

            $this->getRelation()->sync($value);
        });
    }
}
