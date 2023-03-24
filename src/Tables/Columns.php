<?php

namespace Cone\Root\Tables;

use Cone\Root\Tables\Columns\Column;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

class Columns extends Collection
{
    /**
     * Register the given columns.
     */
    public function register(array|Column $columns): static
    {
        foreach (Arr::wrap($columns) as $column) {
            $this->push($column);
        }

        return $this;
    }
}
