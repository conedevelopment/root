<?php

namespace Cone\Root\Fields;

class Textarea extends Field
{
    /**
     * The Vue compoent.
     *
     * @var string
     */
    protected string $component = 'FormTextarea';

    /**
     * Set the rows attribute.
     *
     * @param  int  $value
     * @return $this
     */
    public function rows(int $value): static
    {
        return $this->setAttribute('rows', $value);
    }

    /**
     * Set the cols attribute.
     *
     * @param  int  $value
     * @return $this
     */
    public function cols(int $value): static
    {
        return $this->setAttribute('cols', $value);
    }
}
