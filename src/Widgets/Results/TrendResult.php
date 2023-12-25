<?php

namespace Cone\Root\Widgets\Results;

class TrendResult extends Result
{
    /**
     * The trend data.
     */
    protected array $data = [];

    /**
     * Create a new trend result instance.
     */
    public function __construct(array $data)
    {
        $this->data = $data;
    }

    /**
     * Convert the result as an array.
     */
    public function toArray(): array
    {
        return $this->data;
    }
}
