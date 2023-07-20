<?php

namespace Cone\Root\Interfaces;

use Illuminate\Contracts\Support\Renderable as Contract;
use Illuminate\Http\Request;

interface Renderable extends Contract
{
    /**
     * Get the data for the view.
     */
    public function data(Request $request): array;
}
