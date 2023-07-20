<?php

namespace Cone\Root\Form\Fields;

use Closure;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class Computed extends Field
{
    /**
     * Create a new field instance.
     */
    public function __construct(string $label, Closure $callback)
    {
        parent::__construct($label, Str::random(6));

        $this->value($callback);
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
}
