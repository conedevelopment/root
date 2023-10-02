<?php

namespace Cone\Root\Fields;

class Email extends Text
{
    /**
     * Create a new field instance.
     */
    public function __construct(string $label, string $modelAttribute = null)
    {
        parent::__construct($label, $modelAttribute);

        $this->type('email');
    }
}
