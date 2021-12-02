<?php

namespace Cone\Root\Filters;

use Cone\Root\Traits\Authorizable;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Str;

abstract class Filter implements Arrayable
{
    use Authorizable;

    /**
     * The Vue component.
     *
     * @var string
     */
    protected string $component;

    /**
     * Make a new filter instance.
     *
     * @param  array  ...$parameters
     * @return static
     */
    public static function make(...$parameters): static
    {
        return new static(...$parameters);
    }

    /**
     * Apply the filter on the query.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  mixed  $value
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function apply(Request $request, Builder $query, mixed $value): Builder
    {
        $name = $this->getKey();

        if ($query->getModel()->hasNamedScope($name)) {
            $query->getModel()->callNamedScope($name, [$query, $value]);
        }

        return $query;
    }

    /**
     * Get the key for the filter.
     *
     * @return string
     */
    public function getKey(): string
    {
        return Str::of(static::class)->classBasename()->kebab();
    }

    /**
     * Get the name.
     *
     * @return string
     */
    public function getName(): string
    {
        return Str::of(static::class)->classBasename()->headline();
    }

    /**
     * Get the Vue component.
     *
     * @return string
     */
    public function getComponent(): string
    {
        return $this->component;
    }

    /**
     * The default value of the filter.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return mixed
     */
    public function default(Request $request): mixed
    {
        return null;
    }

    /**
     * Get the instance as an array.
     *
     * @return array
     */
    public function toArray(): array
    {
        return [
            'component' => $this->getComponent(),
            'default' => App::call([$this, 'default']),
            'key' => $this->getKey(),
            'name' => $this->getName(),
        ];
    }
}
