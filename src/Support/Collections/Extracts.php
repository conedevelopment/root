<?php

namespace Cone\Root\Support\Collections;

use Cone\Root\Extracts\Extract;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

class Extracts extends Collection
{
    /**
     * Register the given extracts.
     */
    public function register(array|Extract $extracts): static
    {
        foreach (Arr::wrap($extracts) as $extract) {
            $this->push($extract);
        }

        return $this;
    }
}
