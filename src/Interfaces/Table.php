<?php

namespace Cone\Root\Interfaces;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;

interface Table
{
    /**
     * Get the per page.
     */
    public function getPerPage(Request $request): ?int;

    /**
     * Get the per page options.
     */
    public function getPerPageOptions(): array;

    /**
     * Get the page name.
     */
    public function getPageName(): string;

    /**
     * Perform the query and the pagination.
     */
    public function paginate(Request $request): LengthAwarePaginator;
}
