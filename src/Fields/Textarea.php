<?php

declare(strict_types = 1);

namespace Cone\Root\Fields;

use Closure;

class Textarea extends Field
{
    /**
     * The Vue component.
     *
     * @var string
     */
    protected string $component = 'Textarea';

    /**
     * Set the rows attribute.
     *
     * @param  int|\Closure  $value
     * @return $this
     */
    public function rows(int|Closure $value): static
    {
        return $this->setAttribute('rows', $value);
    }

    /**
     * Set the cols attribute.
     *
     * @param  int|\Closure  $value
     * @return $this
     */
    public function cols(int|Closure $value): static
    {
        return $this->setAttribute('cols', $value);
    }
}
