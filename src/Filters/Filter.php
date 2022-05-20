<?php

namespace Cone\Root\Filters;

use Cone\Root\Http\Requests\RootRequest;
use Cone\Root\Traits\Authorizable;
use Cone\Root\Traits\Makeable;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

abstract class Filter implements Arrayable
{
    use Authorizable;
    use Makeable;

    /**
     * The Vue component.
     *
     * @var string|null
     */
    protected ?string $component = 'Select';

    /**
     * Indicates if mulitple options can be selected.
     *
     * @var bool
     */
    protected bool $multiple = false;

    /**
     * Apply the filter on the query.
     *
     * @param  \Cone\Root\Http\Requests\RootRequest  $request
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  mixed  $value
     * @return \Illuminate\Database\Eloquent\Builder
     */
    abstract public function apply(RootRequest $request, Builder $query, mixed $value): Builder;

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
     * The default value of the filter.
     *
     * @param  \Cone\Root\Http\Requests\RootRequest  $request
     * @return mixed
     */
    public function default(RootRequest $request): mixed
    {
        $default = $request->query($this->getKey());

        return $this->multiple ? Arr::wrap($default) : $default;
    }

    /**
     * Determine if the filter is active.
     *
     * @param  \Cone\Root\Http\Requests\RootRequest  $request
     * @return bool
     */
    public function active(RootRequest $request): bool
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
     * Get the filter options.
     *
     * @param  \Cone\Root\Http\Requests\RootRequest  $request
     * @return array
     */
    public function options(RootRequest $request): array
    {
        return [];
    }

    /**
     * Set the multiple attribute.
     *
     * @param  bool  $value
     * @return $this
     */
    public function multiple(bool $value = true): static
    {
        $this->multiple = $value;

        return $this;
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
     * @param  \Cone\Root\Http\Requests\RootRequest  $request
     * @return array
     */
    public function toInput(RootRequest $request): array
    {
        $options = $this->options($request);

        return array_merge($this->toArray(), [
            'active' => $this->active($request),
            'default' => $this->default($request),
            'nullable' => true,
            'multiple' => $this->multiple,
            'options' => array_map(static function (mixed $value, mixed $key): array {
                return [
                    'value' => $key,
                    'formatted_value' => $value,
                ];
            }, $options, array_keys($options)),
        ]);
    }
}
