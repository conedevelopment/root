<?php

declare(strict_types=1);

namespace Cone\Root\Fields;

use Closure;
use Cone\Root\Filters\Filter;
use Cone\Root\Filters\RenderableFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class Hidden extends Field
{
    /**
     * The Blade template.
     */
    protected string $template = 'root::fields.hidden';

    /**
     * Create a new field instance.
     */
    public function __construct(string $label, Closure|string|null $modelAttribute = null)
    {
        parent::__construct($label, $modelAttribute);

        $this->type('hidden');
    }

    /**
     * Get the filter representation of the field.
     */
    public function toFilter(): Filter
    {
        return new class($this) extends RenderableFilter
        {
            protected Hidden $field;

            public function __construct(Hidden $field)
            {
                parent::__construct($field->getModelAttribute());

                $this->field = $field;
            }

            public function apply(Request $request, Builder $query, mixed $value): Builder
            {
                return $this->field->resolveFilterQuery($request, $query, $value);
            }

            public function toField(): Hidden
            {
                return Hidden::make($this->field->getLabel(), $this->getRequestKey())
                    ->value(fn (Request $request): mixed => $this->getValue($request));
            }
        };
    }
}
