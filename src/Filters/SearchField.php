<?php

namespace Cone\Root\Filters;

use Closure;
use Cone\Root\Fields\Text;

class SearchField extends Text
{
    /**
     * The Blade template.
     */
    protected string $template = 'root::filters.search';

    /**
     * Create a new field instance.
     */
    public function __construct(string $label, Closure|string|null $modelAttribute = null)
    {
        parent::__construct($label, $modelAttribute);

        $this->class(['search-form__control']);
    }
}
