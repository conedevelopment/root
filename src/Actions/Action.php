<?php

namespace Cone\Root\Actions;

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
     * Collect the resolved fields.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Cone\Root\Support\Collections\Fields
     */
    protected function collectFields(Request $request): Fields
    {
        return Fields::make($this->fields($request));
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
