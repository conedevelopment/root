<?php

namespace Cone\Root\Actions;

use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

class Actions extends Collection
{
    /**
     * Register the given actions.
     */
    public function register(array|Action $actions): static
    {
        foreach (Arr::wrap($actions) as $action) {
            $this->push($action);
        }

        return $this;
    }

    /**
     * Map the action to table components.
     */
    public function mapToTableComponents(Request $request): array
    {
        return $this->map->toTableComponent($request)->all();
    }
}
