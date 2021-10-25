<?php

namespace Cone\Root\Actions;

use Closure;
use Cone\Root\Support\Collections\Fields;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

abstract class Action implements Arrayable
{
    /**
     * Handle the action.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Illuminate\Support\Collection  $models
     * @return \Illuminate\Http\RedirectResponse
     */
    abstract public function handle(Request $request, Collection $models): RedirectResponse;

    /**
     * Get the key for the filter.
     *
     * @return string
     */
    public function getKey(): string
    {
        return Str::of(static::class)->classBasename()->lower()->kebab();
    }

    /**
     * Get the name.
     *
     * @return string
     */
    public function getName(): string
    {
        return Str::of(static::class)->classBasename();
    }

    /**
     * Define the fields for the action.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function fields(Request $request): array
    {
        return [];
    }

    /**
     * Set the fields resolver.
     *
     * @param  array|\Closure  $callback
     * @return $this
     */
    public function withFields(array|Closure $callback): self
    {
        if (is_array($callback)) {
            $callback = static function () use ($callback) {
                return $callback;
            };
        }

        $this->fieldsResolver = $callback;

        return $this;
    }

    /**
     * Collect the resolved fields.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Cone\Root\Support\Collections\Fields
     */
    protected function collectFields(Request $request): Fields
    {
        $fields = Fields::make($this->fields($request));

        if (! is_null($this->fieldsResolver)) {
            $fields = $fields->merge(call_user_func_array($this->fieldsResolver, [$request]));
        }

        return $fields;
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
        ];
    }
}
