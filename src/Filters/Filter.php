<?php

namespace Cone\Root\Filters;

use Cone\Root\Traits\Authorizable;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

abstract class Filter implements Arrayable
{
    use Authorizable;

    /**
     * The Vue component.
     *
     * @var string|null
     */
    protected ?string $component = null;

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
     * Get the key.
     *
     * @return string
     */
    public function getKey(): string
    {
        return Str::of(static::class)->classBasename()->kebab()->toString();
    }

    /**
     * Get the name.
     *
     * @return string
     */
    public function getName(): string
    {
        return __(Str::of(static::class)->classBasename()->headline()->toString());
    }

    /**
     * Get the Vue component.
     *
     * @return string|null
     */
    public function getComponent(): ?string
    {
        return $this->component;
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
     * The default value of the filter.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return mixed
     */
    public function default(Request $request): mixed
    {
        return $request->query($this->getKey());
    }

    /**
     * Determine if the filter is active.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return bool
     */
    public function active(Request $request): bool
    {
        return ! empty($request->query($this->getKey()));
    }

    /**
     * Determine if the filter is functional.
     *
     * @return bool
     */
    public function functional(): bool
    {
        return is_null($this->getComponent());
    }

    /**
     * Get the instance as an array.
     *
     * @return array
     */
    public function toArray(): array
    {
        return [
            'key' => $this->getKey(),
            'name' => $this->getName(),
            'component' => $this->getComponent(),
        ];
    }

    /**
     * Get the input representation of the filter.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toInput(Request $request): array
    {
        return array_merge($this->toArray(), [
            'active' => $this->active($request),
            'default' => $this->default($request),
        ]);
    }
}
