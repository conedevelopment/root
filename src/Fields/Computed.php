<?php

namespace Cone\Root\Fields;

use Closure;
use Cone\Root\Http\Requests\RootRequest;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Computed extends Field
{
    /**
     * Create a new field instance.
     *
     * @return void
     */
    public function __construct(string $label, Closure $callback)
    {
        parent::__construct($label, Str::random(6));

        $this->value($callback);
        $this->visibleOnDisplay();
    }

    /**
     * {@inheritdoc}
     */
    public function sortable(bool|Closure $value = true): static
    {
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function searchable(bool|Closure $value = true): static
    {
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function persist(RootRequest $request, Model $model): void
    {
        //
    }

    /**
     * {@inheritdoc}
     */
    public function resolveHydrate(RootRequest $request, Model $model, mixed $value): void
    {
        //
    }
}
