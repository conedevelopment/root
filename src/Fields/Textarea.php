<?php

declare(strict_types=1);

namespace Cone\Root\Fields;

use Closure;

class Textarea extends Field
{
    /**
     * The Blade template.
     */
    protected string $template = 'root::fields.textarea';

    /**
     * Create a new field instance.
     */
    public function __construct(string $label, Closure|string|null $modelAttribute = null)
    {
        parent::__construct($label, $modelAttribute);

        $this->class(['form-control']);
    }

    /**
     * Set the rows attribute.
     */
    public function rows(int|Closure $value): static
    {
        return $this->setAttribute('rows', $value);
    }

    /**
     * Set the cols attribute.
     */
    public function cols(int|Closure $value): static
    {
        return $this->setAttribute('cols', $value);
    }
}
