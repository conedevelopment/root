<?php

namespace Cone\Root\Interfaces;

use Cone\Root\Tables\Table;
use Illuminate\Http\Request;

interface HasTable
{
    /**
     * Get the table representation of the resource.
     */
    public function toTable(Request $request): Table;
}
