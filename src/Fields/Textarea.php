<?php

namespace Cone\Root\Fields;

class Textarea extends Field
{
    /**
     * Set the rows attribute.
     *
     * @param  int  $rows
     * @return $this
     */
    public function rows(int $value): self
    {
        return $this->setAttribute('rows', $value);
    }

    /**
     * Set the cols attribute.
     *
     * @param  int  $cols
     * @return $this
     */
    public function cols(int $value): self
    {
        return $this->setAttribute('cols', $value);
    }
}
