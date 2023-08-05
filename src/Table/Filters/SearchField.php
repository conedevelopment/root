<?php

namespace Cone\Root\Table\Filters;

use Cone\Root\Form\Fields\Text;

class SearchField extends Text
{
    /**
     * The Blade template.
     */
    protected string $template = 'root::table.filters.search';
}
