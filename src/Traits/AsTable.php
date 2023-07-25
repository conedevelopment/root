<?php

namespace Cone\Root\Traits;

use Cone\Root\Table\Table;
use Illuminate\Http\Request;

trait AsTable
{
    /**
     * The table instance.
     */
    protected ?Table $table = null;

    /**
     * Get the table instance for the resource.
     */
    abstract public function toTable(Request $request): Table;
}
