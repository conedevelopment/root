<?php

namespace Cone\Root\Interfaces\Support\Collections;

use Illuminate\Http\Request;

interface Resources
{
    /**
     * Filter the available resources.
     */
    public function available(Request $request): static;
}
