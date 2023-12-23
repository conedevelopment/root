<?php

namespace Cone\Root\Widgets\Results;

class ValueResult extends Result
{
    /**
     * The current value.
     */
    protected float $current;

    /**
     * The previous value.
     */
    protected float $previous;

    /**
     * Create a new result instance.
     */
    public function __construct(float $current, float $previous = 0)
    {
        $this->current = round($current, 2);
        $this->previous = round($previous, 2);
    }

    /**
     * Calculate the trend.
     */
    public function trend(): float
    {
        return $this->previous === 0 ? 0 : round(($this->current - $this->previous) / (($this->current + $this->previous) / 2) * 100, 1);
    }

    /**
     * Convert the result as an array.
     */
    public function toArray(): array
    {
        return [
            'previous' => $this->previous,
            'current' => $this->current,
            'trend' => $this->trend(),
        ];
    }
}
