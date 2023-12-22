<?php

namespace Cone\Root\Fields;

use Closure;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class Boolean extends Field
{
    /**
     * The Blade template.
     */
    protected string $template = 'root::fields.boolean';

    /**
     * Create a new file field instance.
     */
    public function __construct(string $label, Closure|string|null $modelAttribute = null)
    {
        parent::__construct($label, $modelAttribute);

        $this->type('checkbox');
    }

    /**
     * {@inheritdoc}
     */
    public function getValueForHydrate(Request $request): mixed
    {
        return $request->boolean($this->getRequestKey());
    }

    /**
     * Set the "checked" HTML attribute.
     */
    public function checked(bool $value = true): static
    {
        $this->setAttribute('checked', $value);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function resolveValue(Request $request, Model $model): mixed
    {
        $value = parent::resolveValue($request, $model);

        $this->checked(filter_var($value, FILTER_VALIDATE_BOOL));

        return $value;
    }
}
