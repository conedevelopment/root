<?php

namespace Cone\Root\Filters;

use Cone\Root\Fields\Text;

class SearchField extends Text
{
    /**
     * The Blade template.
     */
    protected string $template = 'root::filters.search';
}
